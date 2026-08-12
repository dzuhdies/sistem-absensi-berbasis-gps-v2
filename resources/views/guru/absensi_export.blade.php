<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Export Rekap Absensi Guru</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.07);
        }

        .report-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .report-header h2 {
            font-size: 24px;
            color: #2c3e50;
            margin: 0;
        }

        .report-header p {
            font-size: 14px;
            color: #7f8c8d;
            margin: 5px 0 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th,
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
            text-align: left;
            vertical-align: middle;
        }

        thead th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
        }

        tbody tr:hover {
            background-color: #f1f3f5;
        }

        .text-center {
            text-align: center;
        }

        .font-semibold {
            font-weight: 600;
        }

        img.logo {
            width: 200px;
            height: auto;
            margin-bottom: 15px;
        }
        
        img.absen-photo {
            max-width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            display: block;
            margin: 0 auto;
        }

        a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }

        .badge {
            display: inline-block;
            padding: .3em .65em;
            font-size: 11px;
            font-weight: 600;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .375rem;
        }

        .badge-success {
            color: #155724;
            background-color: #d4edda;
        }

        .badge-danger {
            color: #721c24;
            background-color: #f8d7da;
        }

        .badge-warning {
            color: #856404;
            background-color: #fff3cd;
        }

        .badge-secondary {
            color: #383d41;
            background-color: #e2e3e5;
        }

        .badge-info {
            color: #0c5460;
            background-color: #d1ecf1;
        }

        .badge-purple {
            color: #4d2c70;
            background-color: #e6ddeb;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="report-header">
            <img src="{{ asset('logogii.png') }}" alt="Logo PT Global Intermedia" class="logo">
            <h2>Rekap Absensi Siswa</h2>
            <p>
                <strong>Rekap:</strong>
                @if(request('bulan') && request('siswa_id'))
                Laporan Siswa '{{ $siswas->first()->nama_lengkap }}' pada bulan {{ \Carbon\Carbon::parse(request('bulan'))->translatedFormat('F Y') }}
                @elseif(request('bulan'))
                Laporan Bulanan: {{ \Carbon\Carbon::parse(request('bulan'))->translatedFormat('F Y') }}
                @elseif(request('siswa_id'))
                Laporan Harian Siswa '{{ $siswas->first()->nama_lengkap }}' ({{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }})
                @else
                Laporan Harian ({{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }})
                @endif
            </p>
            <p><strong>Laporan Dibuat:</strong> {{ now()->translatedFormat('d F Y, H:i') }}</p>
        </div>

        @if ($displayMode === 'monthly' || $displayMode === 'user_history')
        <table>
            <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th class="text-center">Tanggal</th>
                    <th class="text-center">Jam Masuk</th>
                    <th class="text-center">Tipe Kerja</th>
                    <th class="text-center">Keterangan</th>
                    <th class="text-center">Durasi Kerja / Dokumen</th>
                    <th class="text-center">Jam Pulang</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($siswas as $siswa)
                @forelse ($siswa->absensis as $absen)
                <tr>
                    <td>{{ $siswa->nama_lengkap }}</td>
                    <td class="text-center font-semibold">{{ \Carbon\Carbon::parse($absen->tanggal)->format('d M Y') }}</td>
                    <td class="text-center">{{ $absen->izin ? '-' : ($absen->jam_masuk ?? '-') }}</td>
                    <td class="text-center">
                        @if ($absen->izin)
                        <span class="badge badge-warning">IZIN: {{ $absen->jenisIzin->nama ?? 'Lainnya' }}</span>
                        @else
                        <span class="badge {{ $absen->status_kerja ? 'badge-info' : 'badge-purple' }}">{{ $absen->status_kerja ? 'WFO' : 'WFH' }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($absen->izin)
                        {{ $absen->keterangan_izin ?? '-' }}
                        @elseif ($absen->jam_masuk)
                        @if ($absen->status_ketepatan == 'Terlambat')
                        <span class="badge badge-danger">Terlambat</span>
                        @else
                        <span class="badge badge-success">Tepat Waktu</span>
                        @endif
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($absen->izin && $absen->file_izin)
                        <a href="{{ asset('storage/' . $absen->file_izin) }}" target="_blank">Lihat Dokumen</a>
                        @else
                        {{ $absen->izin ? '-' : ($absen->durasi_kerja ?? '-') }}
                        @endif
                    </td>
                    <td class="text-center">{{ $absen->izin ? '-' : ($absen->jam_pulang ?? '-') }}</td>
                </tr>
                @empty
                <tr>
                    <td>{{ $siswa->nama_lengkap }}</td>
                    <td colspan="6" class="text-center">Tidak ada data absensi ditemukan.</td>
                </tr>
                @endforelse
                @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">Tidak ada siswa yang cocok dengan filter.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @else
        <table>
            <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th class="text-center">Tipe Kerja</th>
                    <th class="text-center">Jam Masuk</th>
                    <th class="text-center">Keterangan</th>
                    <th class="text-center">Durasi Kerja / Dokumen</th>
                    <th class="text-center">Jam Pulang</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($siswas as $siswa)
                @php $absen = $siswa->absensis->first(); @endphp
                <tr>
                    <td>{{ $siswa->nama_lengkap }}</td>
                    <td class="text-center">
                        @if ($absen && $absen->izin)
                        <span class="badge badge-warning">IZIN: {{ $absen->jenisIzin->nama ?? 'Lainnya' }}</span>
                        @elseif ($absen)
                        <span class="badge {{ $absen->status_kerja ? 'badge-info' : 'badge-purple' }}">{{ $absen->status_kerja ? 'WFO' : 'WFH' }}</span>
                        @else - @endif
                    </td>
                    <td class="text-center">{{ ($absen && !$absen->izin) ? $absen->jam_masuk : '-' }}</td>
                    <td class="text-center">
                        @if ($absen && $absen->izin)
                        {{ $absen->keterangan_izin ?? '-' }}
                        @elseif ($absen && $absen->jam_masuk)
                        @if ($absen->status_ketepatan == 'Terlambat')
                        <span class="badge badge-danger">Terlambat</span>
                        @else
                        <span class="badge badge-success">Tepat Waktu</span>
                        @endif
                        @else
                        <span class="badge badge-secondary">Tidak Hadir</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($absen && $absen->izin)
                        @if ($absen->file_izin)
                        <a href="{{ asset('storage/' . $absen->file_izin) }}" target="_blank">Lihat Dokumen</a>
                        @else - @endif
                        @elseif ($absen)
                        {{ $absen->durasi_kerja ?? '-' }}
                        @else - @endif
                    </td>
                    <td class="text-center">{{ ($absen && !$absen->izin) ? $absen->jam_pulang : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px;">Tidak ada siswa yang terdaftar untuk ditampilkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @endif
    </div>

</body>
</html>