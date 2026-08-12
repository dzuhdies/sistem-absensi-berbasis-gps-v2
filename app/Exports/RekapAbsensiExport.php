<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapAbsensiExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function __construct(
        private readonly Collection $siswas,
        private readonly ?string $tanggal = null
    ) {
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->siswas as $siswa) {
            if ($siswa->absensis->isEmpty()) {
                $rows[] = $this->emptyRow($siswa->nama_lengkap);
                continue;
            }

            foreach ($siswa->absensis as $absen) {
                $izin = (bool) $absen->izin;

                $rows[] = [
                    $siswa->nama_lengkap,
                    $absen->tanggal ? Carbon::parse($absen->tanggal)->format('d-m-Y') : '-',
                    $izin ? '-' : ($absen->jam_masuk ?? '-'),
                    $izin ? '-' : ($absen->jam_pulang ?? '-'),
                    $izin ? 'Izin' : ($absen->status_kehadiran ? 'Hadir' : 'Tidak Hadir'),
                    $izin ? '-' : ($absen->status_ketepatan ?? '-'),
                    $izin ? '-' : ($absen->status_kerja ? 'WFO' : 'WFH'),
                    $izin ? '-' : ($absen->durasi_kerja ?? '-'),
                    $izin ? ($absen->keterangan_izin ?? '-') : '-',
                    $absen->foto_masuk_url ?? '-',
                    $absen->foto_keluar_url ?? '-',
                    $this->locationUrl($absen->lokasi_masuk_lat, $absen->lokasi_masuk_long),
                ];
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'Tanggal',
            'Jam Masuk',
            'Jam Pulang',
            'Status Kehadiran',
            'Ketepatan',
            'Tipe Kerja',
            'Durasi Kerja',
            'Keterangan Izin',
            'Foto Masuk',
            'Foto Keluar',
            'Lokasi Masuk',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->freezePane('A2');
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    private function emptyRow(string $nama): array
    {
        return [
            $nama,
            $this->tanggal ? Carbon::parse($this->tanggal)->format('d-m-Y') : '-',
            '-',
            '-',
            'Tidak Hadir',
            '-',
            '-',
            '-',
            '-',
            '-',
            '-',
            '-',
        ];
    }

    private function locationUrl($latitude, $longitude): string
    {
        return $latitude && $longitude
            ? "https://www.google.com/maps?q={$latitude},{$longitude}"
            : '-';
    }
}
