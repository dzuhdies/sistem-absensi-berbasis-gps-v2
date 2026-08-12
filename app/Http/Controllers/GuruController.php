<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
use App\Models\SiswaProfile;
use Carbon\Carbon;
use App\Models\Setting;
use App\Models\User;
use App\Exports\RekapAbsensiExport;
use Maatwebsite\Excel\Facades\Excel;

class GuruController extends Controller
{
    public function rekapAbsensi(Request $request)
    {
        $guru = Auth::user();
        $jamTelat = Setting::where('key', 'jam_telat')->value('value') ?? '08:16:00';
        $siswaId = $request->input('siswa_id');
        $bulan = $request->input('bulan');
        $tanggal = $request->input('tanggal', $request->has('bulan') ? null : now()->toDateString());
        $all_siswas = $guru->siswaYangDiawasi()->orderBy('nama_lengkap')->get();
        $querySiswa = $guru->siswaYangDiawasi();
        if ($siswaId) {
            $querySiswa->where('siswa_profiles.id', $siswaId);
        }
        $absensiQuery = function ($q) use ($tanggal, $bulan) {
            if ($bulan) {
                $date = Carbon::parse($bulan);
                $q->whereYear('tanggal', $date->year)->whereMonth('tanggal', $date->month);
            } elseif ($tanggal) {
                $q->whereDate('tanggal', $tanggal);
            }
            $q->orderBy('tanggal', 'desc');
        };
        if ($bulan || ($siswaId && !$tanggal)) {
            $displayMode = 'monthly';
        } else {
            $displayMode = 'daily';
        }
        $siswas = $querySiswa->with(['absensis' => $absensiQuery])
            ->orderBy('nama_lengkap')
            ->get();
        $this->setStudentAbsensiPhotoUrls($siswas);

        return view('guru.guru', compact(
            'siswas',
            'all_siswas',
            'tanggal',
            'bulan',
            'siswaId',
            'displayMode',
            'jamTelat'
        ));
    }

    public function exportRekap(Request $request)
    {
        $guru = Auth::user();
        $jamTelat = Setting::where('key', 'jam_telat')->value('value') ?? '08:16:00';
        $tanggal = $request->input('tanggal', now()->toDateString());

        $query = $guru->siswaYangDiawasi();

        $displayMode = 'daily';
        if ($request->filled('bulan')) {
            $displayMode = 'monthly';
            list($tahun, $bulan) = explode('-', $request->bulan);
            $query->with(['absensis' => function ($q) use ($bulan, $tahun) {
                $q->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->orderBy('tanggal', 'asc');
            }]);
        } else {
            $query->with(['absensis' => function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal);
            }]);
        }

        if ($request->filled('siswa_id')) {
            $query->where('siswa_profiles.id', $request->siswa_id);
            if ($request->filled('bulan')) {
                $displayMode = 'user_history';
            }
        }

        $siswas = $query->orderBy('nama_lengkap')->get();
        $this->setStudentAbsensiPhotoUrls($siswas);

        $periode = $request->filled('bulan') ? $request->bulan : $tanggal;
        $filename = 'rekap-absensi-guru-' . $periode . '.xlsx';

        return Excel::download(new RekapAbsensiExport($siswas, $tanggal), $filename);
    }
}
