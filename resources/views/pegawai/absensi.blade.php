<!DOCTYPE html>
<html lang="id">

<head>
    <title>Rekap Absensi Siswa</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.pwa')
</head>

<body class="bg-gray-100">

    <div class="container mx-auto p-4 sm:p-6 lg:p-8">

        {{-- BAGIAN HEADER --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-1 sm:mb-0 flex items-center gap-2">
                Rekap Absensi Siswa
                @if(isset($gedung))
                <span class="text-base sm:text-lg font-medium text-blue-600">(di {{ $gedung->nama }})</span>
                @endif
            </h2>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                    Logout
                </button>
            </form>
        </div>

        {{-- BAGIAN FILTER --}}
        <div class="bg-white p-5 rounded-xl shadow-md border border-gray-200 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-3 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                </svg>
                Filter Data Absensi
            </h3>
            <form method="GET" action="{{ route('pegawai.absensi.rekap') }}">
                @php
                $selectedYear = request('bulan') ? \Carbon\Carbon::parse(request('bulan'))->year : '';
                $selectedMonth = request('bulan') ? \Carbon\Carbon::parse(request('bulan'))->month : '';
                @endphp
                <input type="hidden" name="bulan" id="hidden_bulan" value="{{ request('bulan') }}">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-5 items-end">
                    {{-- Filter Siswa --}}
                    <div>
                        <label for="siswa_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Siswa</label>
                        <select id="siswa_id" name="siswa_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition">
                            <option value="">Semua Siswa</option>
                            @foreach ($all_siswas as $s)
                            <option value="{{ $s->id }}" {{ request('siswa_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_lengkap }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Tanggal --}}
                    <div>
                        <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal</label>
                        <input type="date" id="tanggal" name="tanggal" value="{{ request('tanggal', $tanggal) }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition">
                    </div>

                    {{-- Filter Bulan dan Tahun --}}
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="filter_bulan" class="block text-sm font-medium text-gray-700 mb-1">Pilih Bulan</label>
                            <select id="filter_bulan" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition">
                                <option value="">Bulan</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $selectedMonth == $i ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                    </option>
                                    @endfor
                            </select>
                        </div>
                        <div>
                            <label for="filter_tahun" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                            <select id="filter_tahun" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition">
                                <option value="">Tahun</option>
                                @for ($year = now()->year; $year >= now()->year - 2; $year--)
                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                                @endfor
                            </select>
                        </div>
                    </div>


                    {{-- Tombol Aksi --}}
                    <div class="flex items-center gap-2 w-full">
                        <button type="submit" class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                            <span>Filter</span>
                        </button>
                        <a href="{{ route('pegawai.absensi.rekap') }}" class="flex items-center gap-1 justify-center p-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded-lg shadow-md transition-all duration-200" title="Reset Filter">
                            Reset
                        </a>
                        <a href="{{ route('pegawai.absensi.export', request()->query()) }}" class="w-full flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-all duration-200" title="Download Excel">
                            <span>Download Excel</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- BAGIAN TABEL DATA --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                {{-- Tampilan Tabel Bulanan / History per Siswa --}}
                @if ($displayMode === 'monthly' || $displayMode === 'user_history')
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe Kerja</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi Kerja</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Pulang</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Foto Masuk</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Foto Keluar</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($siswas as $siswa)
                        @forelse ($siswa->absensis as $absen)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $siswa->nama_lengkap }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center font-semibold">{{ \Carbon\Carbon::parse($absen->tanggal)->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">{{ $absen->izin ? '-' : ($absen->jam_masuk ?? '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                @if ($absen && $absen->izin)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    IZIN: {{ $absen->jenisIzin->nama ?? 'Lainnya' }}
                                </span>
                                @elseif ($absen)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $absen->status_kerja ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $absen->status_kerja ? 'WFO' : 'WFH' }}
                                </span>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                @if ($absen && $absen->izin)
                                <div class="text-xs text-gray-700">{{ $absen->keterangan_izin ?? '-' }}</div>
                                @elseif ($absen && $absen->jam_masuk)
                                @if ($absen->status_ketepatan == 'Terlambat')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Terlambat</span>
                                @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Tepat Waktu</span>
                                @endif
                                @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">Tidak Hadir</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold text-center">{{ $absen->izin ? '-' : ($absen->durasi_kerja ?? '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">{{ $absen->izin ? '-' : ($absen->jam_pulang ?? '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if ($absen->foto_masuk_url && !$absen->izin)
                                <img src="{{ $absen->foto_masuk_url }}" onclick="showImageModal('{{ $absen->foto_masuk_url }}')" class="w-12 h-12 object-cover rounded-md mx-auto cursor-pointer hover:opacity-80 transition" alt="Foto Masuk">
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if ($absen->lokasi_masuk_lat && !$absen->izin)
                                <button onclick="showMapModal('{{ $absen->lokasi_masuk_lat }}', '{{ $absen->lokasi_masuk_long }}')" class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full hover:bg-blue-200 transition">Lihat Peta</button>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if ($absen->foto_keluar_url && !$absen->izin)
                                <img src="{{ $absen->foto_keluar_url }}" onclick="showImageModal('{{ $absen->foto_keluar_url }}')" class="w-12 h-12 object-cover rounded-md mx-auto cursor-pointer hover:opacity-80 transition" alt="Foto Keluar">
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $siswa->nama_lengkap }}</td>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500 text-sm">Tidak ada data absensi ditemukan.</td>
                        </tr>
                        @endforelse
                        @empty
                        <tr>
                            <td colspan="10" class="px-6 py-10 text-center text-gray-500">Tidak ada siswa yang terdaftar untuk ditampilkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @else
                {{-- Tampilan Tabel Harian --}}
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe Kerja</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi Kerja</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Pulang</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Foto Masuk</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi Masuk</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Foto Keluar</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($siswas as $siswa)
                        @php $absen = $siswa->absensis->first(); @endphp
                        <tr>
                            {{-- Nama Siswa --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $siswa->nama_lengkap }}</td>

                            {{-- Kolom Tipe Kerja --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                @if ($absen && $absen->izin)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    IZIN: {{ $absen->jenisIzin->nama ?? 'Lainnya' }}
                                </span>
                                @elseif ($absen)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $absen->status_kerja ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $absen->status_kerja ? 'WFO' : 'WFH' }}
                                </span>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            {{-- Jam Masuk --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">{{ ($absen && !$absen->izin) ? $absen->jam_masuk : '-' }}</td>

                            {{-- Kolom Keterangan --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                @if ($absen && $absen->izin)
                                <div class="text-xs text-gray-700">{{ $absen->keterangan_izin ?? '-' }}</div>
                                @elseif ($absen && $absen->jam_masuk)
                                @if ($absen->status_ketepatan == 'Terlambat')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Terlambat</span>
                                @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Tepat Waktu</span>
                                @endif
                                @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">Tidak Hadir</span>
                                @endif
                            </td>

                            {{-- Kolom Durasi Kerja --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold text-center">
                                @if ($absen && $absen->izin)
                                @if ($absen->file_izin)
                                <a href="{{ asset('storage/' . $absen->file_izin) }}" target="_blank" class="text-blue-600 hover:underline font-normal text-xs">
                                    Lihat Dokumen
                                </a>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                                @elseif ($absen)
                                {{ $absen->durasi_kerja ?? '-' }}
                                @else
                                -
                                @endif
                            </td>

                            {{-- Jam Pulang --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">{{ ($absen && !$absen->izin) ? $absen->jam_pulang : '-' }}</td>

                            {{-- Foto Masuk --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if ($absen && $absen->foto_masuk_url && !$absen->izin)
                                <img src="{{ $absen->foto_masuk_url }}" onclick="showImageModal('{{ $absen->foto_masuk_url }}')" class="w-12 h-12 object-cover rounded-md mx-auto cursor-pointer hover:opacity-80 transition" alt="Foto Masuk">
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            {{-- Lokasi Masuk --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if ($absen && $absen->lokasi_masuk_lat && !$absen->izin)
                                <button onclick="showMapModal('{{ $absen->lokasi_masuk_lat }}', '{{ $absen->lokasi_masuk_long }}')" class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full hover:bg-blue-200 transition">Lihat Peta</button>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            {{-- Foto Keluar --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if ($absen && $absen->foto_keluar_url && !$absen->izin)
                                <img src="{{ $absen->foto_keluar_url }}" onclick="showImageModal('{{ $absen->foto_keluar_url }}')" class="w-12 h-12 object-cover rounded-md mx-auto cursor-pointer hover:opacity-80 transition" alt="Foto Keluar">
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-10 text-center text-gray-500">Tidak ada siswa yang terdaftar untuk ditampilkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL (TIDAK ADA PERUBAHAN) --}}
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 hidden">
        <div class="relative">
            <img id="modalImage" src="" class="max-w-full max-h-screen rounded-lg border-4 border-white">
            <button onclick="closeModal()" class="absolute top-2 right-2 bg-white rounded-full p-1 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-black" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
    <div id="mapModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl transform transition-all p-4">
            <div class="flex justify-between items-center pb-3 border-b">
                <h4 class="text-lg font-semibold text-gray-800">Lokasi Absen Siswa</h4>
                <button onclick="closeMapModal()" class="text-gray-500 hover:text-gray-800 text-2xl leading-none">×</button>
            </div>
            <div class="mt-4">
                <div id="map-container" class="w-full h-80 bg-gray-200 rounded-lg flex items-center justify-center">
                    <p class="text-gray-500">Memuat peta...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- Modal ---
        function showImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        function showMapModal(lat, long) {
            const mapContainer = document.getElementById('map-container');
            const mapUrl = `https://maps.google.com/maps?q=${lat},${long}&hl=id&z=17&output=embed`;
            mapContainer.innerHTML = `<iframe class="w-full h-full border-0 rounded-lg" src="${mapUrl}" allowfullscreen></iframe>`;
            document.getElementById('mapModal').classList.remove('hidden');
        }

        function closeMapModal() {
            document.getElementById('mapModal').classList.add('hidden');
        }

        document.getElementById('imageModal').addEventListener('click', (e) => e.target === e.currentTarget && closeModal());
        document.getElementById('mapModal').addEventListener('click', (e) => e.target === e.currentTarget && closeMapModal());

        // --- Filter Logic ---
        document.addEventListener('DOMContentLoaded', function() {
            const tanggalInput = document.getElementById('tanggal');
            const filterBulan = document.getElementById('filter_bulan');
            const filterTahun = document.getElementById('filter_tahun');
            const hiddenBulan = document.getElementById('hidden_bulan');

            function updateHiddenBulan() {
                const bulan = filterBulan.value;
                const tahun = filterTahun.value;

                if (bulan && tahun) {
                    // **PERBAIKAN DI SINI**
                    // Mengirim tanggal 1 untuk menghindari bug parsing tanggal di backend
                    hiddenBulan.value = `${tahun}-${bulan}-01`;
                    tanggalInput.value = ''; // Hapus filter tanggal jika bulan dipilih
                } else {
                    hiddenBulan.value = '';
                }
            }

            tanggalInput.addEventListener('input', function() {
                if (this.value) {
                    filterBulan.value = '';
                    filterTahun.value = '';
                    hiddenBulan.value = ''; // Hapus filter bulan jika tanggal dipilih
                }
            });

            filterBulan.addEventListener('change', updateHiddenBulan);
            filterTahun.addEventListener('change', updateHiddenBulan);
        });
    </script>
</body>

</html>
