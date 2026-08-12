<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Absensi;
use App\Helpers\LogHelper;
use App\Models\Setting;
use Carbon\Carbon;
use App\Models\JenisIzin;
use Illuminate\Validation\ValidationException;
    

class SiswaController extends Controller
{
    private const MAX_FOTO_BYTES = 200 * 1024;
    private const MAX_FOTO_DIMENSION = 1280;

    /**
     * Menampilkan foto absensi dari storage untuk hosting yang tidak
     * mendukung symbolic link public/storage.
     */
    public function showFotoAbsensi(string $path)
    {
        if (!preg_match('#^\d{4}-\d{2}/[a-zA-Z0-9._-]+\.(?:jpe?g|png|webp)$#i', $path)) {
            abort(404);
        }

        $storagePath = $this->absensiPhotoPath($path);
        $disk = Storage::disk(self::ABSENSI_PHOTO_DISK);

        abort_unless($disk->exists($storagePath), 404);

        return $disk->response($storagePath, null, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }

    /**
     * Menampilkan halaman absensi siswa.
     */
    public function absenForm()
    {
        $user = Auth::user();
        $siswaProfile = $user->siswaProfile;
        $today = now()->toDateString();
        $jamTelat = Setting::where('key', 'jam_telat')->value('value') ?? '08:16:00';
        $jenisIzin = JenisIzin::all();

        $absenHariIni = $siswaProfile->absensis()->where('tanggal', '>=', $today)->where('tanggal', '<', now()->addDay()->toDateString())->first();
        $this->setAbsensiPhotoUrls($absenHariIni);

        return view('siswa.absen', [
            'nama' => $siswaProfile->nama_lengkap,
            'absensi' => $absenHariIni,
            'gedung' => $siswaProfile->gedung,
            'serverTime' => now()->getTimestamp(),
            'jamTelat' => $jamTelat,
            'jenisIzin' => $jenisIzin,
        ]);
    }

    public function absenMasuk(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'required|string',
        ]);

        $user = Auth::user();
        $siswaProfile = $user->siswaProfile;
        $gedung = $siswaProfile->gedung;

        $imageData = $this->compressFoto($request->foto);
        $bulanFolder = now()->format('Y-m');
        $filename = 'absen_masuk_' . $user->id . '_' . time() . '.jpg';

        $fotoTersimpan = Storage::disk(self::ABSENSI_PHOTO_DISK)->put(
            $this->absensiPhotoPath("{$bulanFolder}/{$filename}"),
            $imageData
        );

        if (!$fotoTersimpan) {
            throw ValidationException::withMessages([
                'foto' => 'Foto gagal disimpan di server. Silakan hubungi admin hosting.',
            ]);
        }

        $distance = $this->getDistance(
            $request->latitude,
            $request->longitude,
            $gedung->latitude,
            $gedung->longitude
        );

        $isWFO = $distance <= $gedung->radius_meter;

        $jamTelat = Setting::where('key', 'jam_telat')->value('value') ?? '08:16:00';

        $jamMasuk = now()->toTimeString();
        $statusKetepatan = $jamMasuk > $jamTelat ? 'Terlambat' : 'Tepat Waktu';

        $now = Carbon::now();
        $jamMasuk = $now->format('H:i:s');
        $isTelat = $jamMasuk > $jamTelat;

        Absensi::updateOrCreate(
            ['siswa_id' => $siswaProfile->id, 'tanggal' => $now->toDateString()],
            [
                'jam_masuk' => $jamMasuk,
                'foto_masuk' => $bulanFolder . '/' . $filename,
                'lokasi_masuk_lat' => $request->latitude,
                'lokasi_masuk_long' => $request->longitude,
                'status_kerja' => $isWFO,
                'status_kehadiran' => true,
                'status_ketepatan' => $statusKetepatan,
                'izin' => false,
                'jenis_izin_id' => null,
                'keterangan_izin' => null,
                'file_izin' => null,
            ]
        );

        LogHelper::log($request, 'Absen Masuk', 'Siswa melakukan absen masuk pada jam ' . $jamMasuk . ($isTelat ? ' (Terlambat)' : ''));

        return redirect()->route('siswa.absen.form')->with('success', 'Absen masuk berhasil!');
    }


    public function absenKeluar(Request $request)
    {
        $request->validate([
            'foto' => 'required|string',
        ]);

        $user = Auth::user();
        $siswaProfile = $user->siswaProfile;
        $today = now()->toDateString();

        $absen = Absensi::where('siswa_id', $siswaProfile->id)
            ->where('tanggal', $today)
            ->first();

        if ($absen && $absen->jam_masuk && !$absen->jam_pulang) {
            $imageData = $this->compressFoto($request->foto);
            $bulanFolder = now()->format('Y-m');
            $filename = 'absen_keluar_' . $user->id . '_' . time() . '.jpg';


            $fotoTersimpan = Storage::disk(self::ABSENSI_PHOTO_DISK)->put(
                $this->absensiPhotoPath("{$bulanFolder}/{$filename}"),
                $imageData
            );

            if (!$fotoTersimpan) {
                throw ValidationException::withMessages([
                    'foto' => 'Foto gagal disimpan di server. Silakan hubungi admin hosting.',
                ]);
            }

            $absen->update([
                'jam_pulang' => now()->toTimeString(),
                'foto_keluar' => $bulanFolder . '/' . $filename,
            ]);

            LogHelper::log($request, 'Absen Keluar', 'Siswa melakukan absen keluar');

            return redirect()->route('siswa.absen.form')->with('success', 'Absen keluar berhasil!');
        }

        LogHelper::log($request, 'Gagal Absen Keluar', 'Siswa mencoba absen keluar tanpa absen masuk');

        return redirect()->route('siswa.absen.form')->with('error', 'Anda belum bisa melakukan absen keluar saat ini.');
    }

    private function getDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    /**
     * Mengubah foto Base64 menjadi JPEG dengan ukuran maksimal 200 KB.
     */
    private function compressFoto(string $fotoBase64): string
    {
        if (!function_exists('imagecreatefromstring') || !function_exists('imagejpeg')) {
            throw ValidationException::withMessages([
                'foto' => 'Server belum mendukung kompresi foto (ekstensi PHP GD belum aktif).',
            ]);
        }

        $encodedImage = preg_replace('#^data:image/[a-zA-Z0-9.+-]+;base64,#i', '', $fotoBase64);
        $imageData = base64_decode($encodedImage, true);
        $sourceImage = $imageData !== false ? @imagecreatefromstring($imageData) : false;

        if ($sourceImage === false) {
            throw ValidationException::withMessages([
                'foto' => 'Data foto tidak valid. Silakan ambil foto kembali.',
            ]);
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);
        $scale = min(1, self::MAX_FOTO_DIMENSION / max($sourceWidth, $sourceHeight));
        $width = max(1, (int) round($sourceWidth * $scale));
        $height = max(1, (int) round($sourceHeight * $scale));

        $workingImage = $this->resizeFoto($sourceImage, $width, $height);
        imagedestroy($sourceImage);

        // Turunkan kualitas lebih dahulu. Jika masih terlalu besar, kecilkan
        // dimensinya dan ulangi sampai hasil benar-benar di bawah 200 KB.
        for ($resizeAttempt = 0; $resizeAttempt < 10; $resizeAttempt++) {
            for ($quality = 85; $quality >= 35; $quality -= 5) {
                ob_start();
                imagejpeg($workingImage, null, $quality);
                $compressed = ob_get_clean();

                if ($compressed !== false && strlen($compressed) <= self::MAX_FOTO_BYTES) {
                    imagedestroy($workingImage);
                    return $compressed;
                }
            }

            $newWidth = max(1, (int) round(imagesx($workingImage) * 0.85));
            $newHeight = max(1, (int) round(imagesy($workingImage) * 0.85));
            $smallerImage = $this->resizeFoto($workingImage, $newWidth, $newHeight);
            imagedestroy($workingImage);
            $workingImage = $smallerImage;
        }

        imagedestroy($workingImage);

        throw ValidationException::withMessages([
            'foto' => 'Foto tidak dapat dikompres hingga 200 KB. Silakan ambil foto kembali.',
        ]);
    }

    private function resizeFoto($sourceImage, int $width, int $height)
    {
        $resizedImage = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($resizedImage, 255, 255, 255);
        imagefill($resizedImage, 0, 0, $white);

        imagecopyresampled(
            $resizedImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $width,
            $height,
            imagesx($sourceImage),
            imagesy($sourceImage)
        );

        return $resizedImage;
    }

    public function getRiwayat(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer',
        ]);

        $user = Auth::user();
        $siswaProfile = $user->siswaProfile;

        $riwayat = Absensi::where('siswa_id', $siswaProfile->id)
            ->whereYear('tanggal', $request->tahun)
            ->whereMonth('tanggal', $request->bulan)
            ->orderBy('tanggal', 'asc')
            ->get();

        foreach ($riwayat as $absensi) {
            $this->setAbsensiPhotoUrls($absensi);
        }

        return response()->json($riwayat);
    }



    public function absenIzin(Request $request)
    {
        $request->validate([
            'jenis_izin_id' => 'required|exists:jenis_izin,id',
            'keperluan' => 'nullable|string',
            'file_izin' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $user = Auth::user();
        $siswa = $user->siswaProfile;

        $absensiHariIni = Absensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        if ($absensiHariIni?->jam_masuk) {
            return back()->with('error', 'Izin tidak dapat diajukan karena Anda sudah absen masuk hari ini.');
        }

        $path = null;
        if ($request->hasFile('file_izin')) {
            $path = $request->file('file_izin')->store('izin_berkas', 'public');
        }

        Absensi::updateOrCreate(
            ['siswa_id' => $siswa->id, 'tanggal' => now()->toDateString()],
            [
                'status_kehadiran' => false,
                'izin' => true,
                'jenis_izin_id' => $request->jenis_izin_id,
                'keterangan_izin' => $request->keperluan,
                'file_izin' => $path,
            ]
        );


        $jenisIzin = \App\Models\JenisIzin::find($request->jenis_izin_id);
        $namaIzin = $jenisIzin ? $jenisIzin->nama : 'ID: ' . $request->jenis_izin_id;

        $logMessage = 'Siswa "' . $siswa->nama_lengkap . '" mengajukan izin (' . $namaIzin . ') dengan keperluan: ' . ($request->keperluan ?: '-');

        LogHelper::log($request, 'Pengajuan Izin Siswa', $logMessage);

        return back()->with('success', 'Izin berhasil dikirim');
    }
}
