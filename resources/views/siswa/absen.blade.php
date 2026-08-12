<!DOCTYPE html>
<html lang="id">

<head>
    <title>Dashboard Siswa</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.pwa')
</head>

<body class="bg-gray-100 font-sans">

    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Halo, {{ $nama }}</h2>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition-colors duration-200">Logout</button>
            </form>
        </div>
    </header>

    <main class="container mx-auto p-4">

        @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif
        @if (session('error'))
        @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
            <strong class="font-bold">Oops! Terjadi kesalahan:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <p id="clock" class="text-3xl font-bold text-gray-800 text-center mb-2">Memuat...</p>
            <div class="text-center mb-6">
                @if ($gedung)
                <p class="text-gray-600">Jarak dari Gedung: <strong id="distance" class="text-gray-800">Mendapatkan lokasi...</strong></p>
                <div class="mt-2">
                    <span id="statusGedung" class="text-xs font-medium px-2.5 py-0.5 rounded-full">-</span>
                </div>
                <div class="mt-2">
                    <button id="btnBukaPeta" class="text-sm text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200">Lihat Lokasimu</button>
                </div>
                @else
                <span class="bg-red-100 text-red-800 text-sm font-medium px-2.5 py-1 rounded-full">Gedung tidak ditemukan.</span>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-4">
                <button id="btnAbsenMasuk"
                    class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-lg shadow-md transition-transform transform hover:scale-105 disabled:bg-gray-300 disabled:cursor-not-allowed disabled:transform-none"
                    @if($absensi && ($absensi->jam_masuk || $absensi->izin)) disabled @endif>
                    Absen Masuk
                </button>
                <button id="btnAbsenKeluar"
                    class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 rounded-lg shadow-md transition-transform transform hover:scale-105 disabled:bg-gray-300 disabled:cursor-not-allowed disabled:transform-none"
                    @if(!$absensi || !$absensi->jam_masuk || $absensi->jam_pulang || ($absensi && $absensi->izin)) disabled @endif>
                    Absen Keluar
                </button>
                <button onclick="showIzinModal()"
                    class="col-span-2 bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-5 rounded-lg disabled:bg-gray-300 disabled:cursor-not-allowed"
                    @if($absensi && ($absensi->jam_masuk || $absensi->izin)) disabled @endif>
                    Ajukan Izin
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-200 pb-3 mb-4">Absensi Hari Ini ({{ now()->format('d M Y') }})</h3>
            <div class="overflow-x-auto">
                @if ($absensi)
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Masuk</th>
                            <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Foto Masuk</th>
                            <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Keluar</th>
                            <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Foto Keluar</th>
                            <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @if ($absensi && $absensi->izin && !$absensi->jam_masuk)
                        <tr>
                            <td colspan="7" class="p-4 text-center">
                                <div class="font-bold text-yellow-800 bg-yellow-100 py-2 px-4 rounded-lg inline-block">
                                    Izin: {{ $absensi->jenisIzin->nama ?? 'Jenis Izin Tidak Ditemukan' }}
                                </div>
                                <div class="mt-2 text-gray-600">
                                    <p><span class="font-semibold">Keterangan:</span> {{ $absensi->keterangan_izin ?: '-' }}</p>
                                    @if ($absensi->file_izin)
                                    <a href="{{ asset('storage/' . $absensi->file_izin) }}"
                                        class="text-blue-600 hover:underline mt-1 inline-block"
                                        target="_blank">
                                        Lihat Berkas
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @else
                        <tr>
                            <td class="py-3 px-3 text-center text-sm text-gray-700">{{ $absensi->jam_masuk ? \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i:s') : '-' }}</td>
                            <td class="py-3 px-3 text-center">
                                @if($absensi->foto_masuk_url)
                                <img src="{{ $absensi->foto_masuk_url }}" alt="Foto Masuk" class="w-16 h-16 object-cover rounded-md mx-auto cursor-pointer hover:scale-105 transition-transform" onclick="showImageModal('{{ $absensi->foto_masuk_url }}')">
                                @else
                                -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                @if ($absensi->status_ketepatan == 'Terlambat')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Terlambat</span>
                                @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Tepat Waktu</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if(isset($absensi->status_kerja))
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $absensi->status_kerja ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $absensi->status_kerja ? 'WFO' : 'WFH' }}
                                </span>
                                @else - @endif
                            </td>
                            <td class="py-3 px-3 text-center text-sm text-gray-700">{{ $absensi->jam_pulang ? \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i:s') : '-' }}</td>
                            <td class="py-3 px-3 text-center">
                                @if($absensi->foto_keluar_url)
                                <img src="{{ $absensi->foto_keluar_url }}" alt="Foto Keluar" class="w-16 h-16 object-cover rounded-md mx-auto cursor-pointer hover:scale-105 transition-transform" onclick="showImageModal('{{ $absensi->foto_keluar_url }}')">
                                @else
                                -
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center text-sm text-gray-700 font-semibold">
                                {{ $absensi->durasi_kerja ?? '-' }}
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                @else
                <p class="text-center text-gray-500 py-4">Anda belum melakukan absensi hari ini.</p>
                @endif
            </div>
        </div>

        <button id="btnBukaRiwayat" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 rounded-lg shadow-md transition-transform transform hover:scale-105">
            Lihat Riwayat Lengkap
        </button>

        <form method="POST" action="{{ route('siswa.absen.masuk',[], true )}}" id="formMasuk" class="hidden">
            @csrf
            <input type="hidden" name="latitude" id="lat">
            <input type="hidden" name="longitude" id="long">
            <input type="hidden" name="foto" id="fotoMasuk">
        </form>
        <form method="POST" action="{{ route('siswa.absen.keluar',[], true)}}" id="formKeluar" class="hidden">
            @csrf
            <input type="hidden" name="foto" id="fotoKeluar">
        </form>
    </main>

    <div id="cameraModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex flex-col justify-center items-center p-4">
        <video id="videoPreview" autoplay muted playsinline class="border-4 border-white rounded-lg w-full max-w-md transform -scale-x-100"></video>
        <canvas id="canvas" class="hidden"></canvas>
        <button id="switchCameraBtn" type="button" class="mt-4 bg-white hover:bg-gray-100 text-gray-800 font-semibold py-2 px-5 rounded-full shadow-lg">
            Gunakan Kamera Belakang
        </button>
        <button id="captureBtn" class="mt-2 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-full shadow-lg">Ambil Foto</button>
        <button id="closeCameraBtn" class="mt-2 bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-full">Batal</button>
    </div>

    <div id="historyModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex justify-center items-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl transform transition-all">
            <div class="flex justify-between items-center p-4 border-b border-gray-200">
                <h4 class="text-lg font-semibold text-gray-800">Riwayat Absensi Bulanan</h4>
                <button id="closeHistoryModal" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">×</button>
            </div>
            <div class="p-4">
                <div class="flex flex-col sm:flex-row gap-4 mb-4">
                    <select id="selectBulan" class="block w-full p-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"></select>
                    <select id="selectTahun" class="block w-full p-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"></select>
                </div>
                <div class="overflow-x-auto overflow-y-auto max-h-[60vh]">
                    <table id="riwayatTable" class="min-w-full bg-white">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Masuk</th>
                                <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Keluar</th>
                                <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi</th>
                                <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            </tr>
                        </thead>
                        <tbody id="riwayatTableBody" class="divide-y divide-gray-200">
                            <tr>
                                <td colspan="6" class="text-center p-4 text-gray-500">Pilih bulan dan tahun untuk melihat riwayat.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="mapModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex justify-center items-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg transform transition-all">
            <div class="flex justify-between items-center p-4 border-b border-gray-200">
                <h4 class="text-lg font-semibold text-gray-800">Lokasi Anda Saat Ini</h4>
                <button id="closeMapModal" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">×</button>
            </div>
            <div class="p-4">
                <div id="map" class="w-full h-80 rounded-lg border border-gray-200">Memuat peta...</div>
            </div>
        </div>
    </div>

    <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
        <div class="relative bg-white p-4 rounded-lg shadow-xl">
            <button onclick="closeImageModal()" class="absolute top-0 right-0 mt-2 mr-2 text-gray-600 hover:text-black text-xl font-bold">×</button>
            <img id="modalImage" src="" alt="Preview" class="max-w-full max-h-[80vh] rounded">
        </div>
    </div>

    <div id="izinModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <form method="POST" action="{{ route('siswa.absen.izin') }}" enctype="multipart/form-data" class="bg-white p-6 rounded shadow w-full max-w-md">
            @csrf
            <h3 class="text-lg font-bold mb-4">Form Izin</h3>
            <label>Jenis Izin</label>
            <select name="jenis_izin_id" required class="w-full border rounded mb-2">
                @foreach($jenisIzin as $izin)
                <option value="{{ $izin->id }}">{{ $izin->nama }}</option>
                @endforeach
            </select>
            <label>Keterangan</label>
            <textarea name="keperluan" class="w-full border rounded mb-2"></textarea>
            <label>Upload Berkas (opsional)</label>
            <input type="file" name="file_izin" class="w-full mb-4">
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('izinModal')" class="bg-gray-300 px-3 py-1 rounded">Batal</button>
                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Kirim</button>
            </div>
        </form>
    </div>

    <script>
        function showImageModal(imageSrc) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = imageSrc;
            modal.classList.remove('hidden');
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = '';
            modal.classList.add('hidden');
        }
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });


        document.addEventListener('DOMContentLoaded', function() {
            const clockEl = document.getElementById('clock');
            const latInput = document.getElementById('lat');
            const longInput = document.getElementById('long');
            const distanceEl = document.getElementById('distance');
            const statusGedungEl = document.getElementById('statusGedung');
            const btnAbsenMasuk = document.getElementById('btnAbsenMasuk');
            const btnAbsenKeluar = document.getElementById('btnAbsenKeluar');
            const formMasuk = document.getElementById('formMasuk');
            const formKeluar = document.getElementById('formKeluar');
            const fotoMasukInput = document.getElementById('fotoMasuk');
            const fotoKeluarInput = document.getElementById('fotoKeluar');
            const cameraModal = document.getElementById('cameraModal');
            const video = document.getElementById('videoPreview');
            const canvas = document.getElementById('canvas');
            const captureBtn = document.getElementById('captureBtn');
            const switchCameraBtn = document.getElementById('switchCameraBtn');
            const closeCameraBtn = document.getElementById('closeCameraBtn');
            const historyModal = document.getElementById('historyModal');
            const btnBukaRiwayat = document.getElementById('btnBukaRiwayat');
            const closeHistoryModal = document.getElementById('closeHistoryModal');
            const mapModal = document.getElementById('mapModal');
            const mapEl = document.getElementById('map');
            const btnBukaPeta = document.getElementById('btnBukaPeta');
            const closeMapModal = document.getElementById('closeMapModal');

            let stream;
            let currentFacingMode = 'user';
            let currentFormToSubmit = null;
            const gedLat = parseFloat("{{ $gedung->latitude ?? 0 }}");
            const gedLong = parseFloat("{{ $gedung->longitude ?? 0 }}");
            const gedRadius = parseFloat("{{ $gedung->radius_meter ?? 10 }}");

            function updateClock() {
                const now = new Date();
                const options = {
                    timeZone: 'Asia/Jakarta',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                };
                clockEl.textContent = now.toLocaleTimeString('id-ID', options);
            }
            setInterval(updateClock, 1000);
            updateClock();

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        const lat = pos.coords.latitude;
                        const long = pos.coords.longitude;
                        latInput.value = lat;
                        longInput.value = long;
                        mapEl.innerHTML = `<iframe class="w-full h-full" src="https://maps.google.com/maps?q=${lat},${long}&hl=id&z=17&output=embed" frameborder="0" allowfullscreen></iframe>`;

                        if (gedLat && gedLong && gedRadius) {
                            const jarak = getDistanceFromLatLonInMeters(lat, long, gedLat, gedLong);
                            distanceEl.textContent = jarak.toFixed(0) + ' meter';

                            if (jarak <= gedRadius) {
                                statusGedungEl.textContent = 'Di Dalam Area';
                                statusGedungEl.className = 'bg-green-100 text-green-800 text-sm font-medium px-2.5 py-1 rounded-full';
                            } else {
                                statusGedungEl.textContent = 'Di Luar Area';
                                statusGedungEl.className = 'bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-1 rounded-full';
                            }
                        }

                    },
                    (err) => {
                        distanceEl.textContent = 'Gagal';
                        statusGedungEl.textContent = 'Izin lokasi ditolak';
                        statusGedungEl.className = 'bg-red-100 text-red-800 text-sm font-medium px-2.5 py-1 rounded-full';
                        if (btnAbsenMasuk) btnAbsenMasuk.disabled = true;
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                distanceEl.textContent = 'Tidak didukung.';
                statusGedungEl.className = 'bg-gray-100 text-gray-800 text-sm font-medium px-2.5 py-1 rounded-full';
            }

            function getDistanceFromLatLonInMeters(lat1, lon1, lat2, lon2) {
                const R = 6371e3;
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLon = (lon2 - lon1) * Math.PI / 180;
                const a = Math.sin(dLat / 2) ** 2 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon / 2) ** 2;
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c;
            }

            async function startCamera() {
                try {
                    if (stream) stream.getTracks().forEach(track => track.stop());
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: { ideal: currentFacingMode }
                        },
                        audio: false
                    });
                    video.srcObject = stream;
                    video.classList.toggle('-scale-x-100', currentFacingMode === 'user');
                    switchCameraBtn.textContent = currentFacingMode === 'user'
                        ? 'Gunakan Kamera Belakang'
                        : 'Gunakan Kamera Depan';
                    await video.play();
                    cameraModal.classList.remove('hidden');
                } catch (e) {
                    alert('Gagal mengakses kamera: ' + e.message);
                }
            }

            function stopCamera() {
                if (stream) stream.getTracks().forEach(track => track.stop());
                stream = null;
                video.srcObject = null;
                cameraModal.classList.add('hidden');
            }

            if (btnAbsenMasuk) btnAbsenMasuk.addEventListener('click', () => {
                currentFormToSubmit = formMasuk;
                startCamera();
            });
            if (btnAbsenKeluar) btnAbsenKeluar.addEventListener('click', () => {
                currentFormToSubmit = formKeluar;
                startCamera();
            });
            closeCameraBtn.addEventListener('click', stopCamera);
            switchCameraBtn.addEventListener('click', async () => {
                currentFacingMode = currentFacingMode === 'user' ? 'environment' : 'user';
                await startCamera();
            });

            function base64ImageSize(dataUrl) {
                const base64 = dataUrl.split(',')[1] || '';
                const padding = (base64.match(/=*$/) || [''])[0].length;
                return Math.floor((base64.length * 3) / 4) - padding;
            }

            function compressCameraPhoto() {
                const maxBytes = 200 * 1024;
                const maxDimension = 1280;
                const scale = Math.min(1, maxDimension / Math.max(video.videoWidth, video.videoHeight));

                canvas.width = Math.max(1, Math.round(video.videoWidth * scale));
                canvas.height = Math.max(1, Math.round(video.videoHeight * scale));

                const context = canvas.getContext('2d');
                if (currentFacingMode === 'user') {
                    context.save();
                    context.scale(-1, 1);
                    context.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
                    context.restore();
                } else {
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
                }

                let workingCanvas = canvas;

                for (let resizeAttempt = 0; resizeAttempt < 10; resizeAttempt++) {
                    for (let quality = 0.85; quality >= 0.35; quality -= 0.05) {
                        const base64 = workingCanvas.toDataURL('image/jpeg', quality);
                        if (base64ImageSize(base64) <= maxBytes) return base64;
                    }

                    const smallerCanvas = document.createElement('canvas');
                    smallerCanvas.width = Math.max(1, Math.round(workingCanvas.width * 0.85));
                    smallerCanvas.height = Math.max(1, Math.round(workingCanvas.height * 0.85));
                    smallerCanvas.getContext('2d').drawImage(
                        workingCanvas,
                        0,
                        0,
                        smallerCanvas.width,
                        smallerCanvas.height
                    );
                    workingCanvas = smallerCanvas;
                }

                throw new Error('Foto tidak dapat dikompres hingga 200 KB.');
            }

            captureBtn.addEventListener('click', () => {
                if (!currentFormToSubmit) return;
                captureBtn.disabled = true;

                try {
                    const base64 = compressCameraPhoto();

                    if (currentFormToSubmit === formMasuk) fotoMasukInput.value = base64;
                    else fotoKeluarInput.value = base64;

                    stopCamera();
                    currentFormToSubmit.submit();
                } catch (error) {
                    alert(error.message || 'Gagal mengompres foto. Silakan coba lagi.');
                    captureBtn.disabled = false;
                }
            });

            const selectBulan = document.getElementById('selectBulan');
            const selectTahun = document.getElementById('selectTahun');
            const riwayatTableBody = document.getElementById('riwayatTableBody');
            const bulanNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

            function populateSelectors() {
                const now = new Date();
                const currentMonth = now.getMonth();
                const currentYear = now.getFullYear();
                bulanNames.forEach((nama, index) => {
                    const option = new Option(nama, index + 1);
                    if (index === currentMonth) option.selected = true;
                    selectBulan.add(option);
                });
                for (let i = currentYear; i >= currentYear - 2; i--) {
                    selectTahun.add(new Option(i, i));
                }
            }

            async function fetchRiwayat() {
                const bulan = selectBulan.value;
                const tahun = selectTahun.value;
                riwayatTableBody.innerHTML = `<tr><td colspan="6" class="text-center p-4 text-gray-500">Memuat data...</td></tr>`;

                try {
                    const response = await fetch(`${window.location.origin}/siswa/riwayat?bulan=${bulan}&tahun=${tahun}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                    const data = await response.json();
                    riwayatTableBody.innerHTML = '';

                    if (data.length > 0) {
                        data.forEach(d => {
                            let statusHadir;
                            if (d.jam_masuk) {
                                statusHadir = d.status_ketepatan === 'Terlambat' ?
                                    '<span class="text-red-600 font-semibold">Terlambat</span>' :
                                    '<span class="text-green-600 font-semibold">Tepat Waktu</span>';
                            } else if (d.izin === true || Number(d.izin) === 1) {
                                statusHadir = `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Izin</span>`;
                            } else {
                                statusHadir = '-';
                            }

                            const tipeKerja = d.jam_masuk ? `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${d.status_kerja ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800'}">${d.status_kerja ? 'WFO' : 'WFH'}</span>` : '-';
                            const tgl = new Date(d.tanggal).toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            });
                            let durasiKerja = d.durasi_kerja || '-';
                            if (!d.durasi_kerja && d.jam_masuk && d.jam_pulang) {
                                const masuk = new Date(`1970-01-01T${d.jam_masuk}`);
                                const pulang = new Date(`1970-01-01T${d.jam_pulang}`);
                                const diffMs = pulang - masuk;
                                if (diffMs > 0) {
                                    const diffHrs = Math.floor(diffMs / 3600000);
                                    const diffMins = Math.round((diffMs % 3600000) / 60000);
                                    durasiKerja = `${diffHrs} jam ${diffMins} menit`;
                                }
                            }

                            const row = riwayatTableBody.insertRow();
                            row.innerHTML = `
<td class="py-3 px-3 text-center text-sm text-gray-700 whitespace-nowrap">${tgl}</td>
<td class="py-3 px-3 text-center text-sm text-gray-700">${d.jam_masuk ? d.jam_masuk.substring(0, 5) : '-'}</td>
<td class="py-3 px-3 text-center text-sm text-gray-700">${d.jam_pulang ? d.jam_pulang.substring(0, 5) : '-'}</td>
<td class="py-3 px-3 text-center text-sm text-gray-700 font-semibold">${durasiKerja}</td>
<td class="py-3 px-3 text-center text-sm">${statusHadir}</td>
<td class="py-3 px-3 text-center">${tipeKerja}</td>
`;
                        });
                    } else {
                        riwayatTableBody.innerHTML = `<tr><td colspan="6" class="text-center p-4 text-gray-500">Tidak ada data untuk periode ini.</td></tr>`;
                    }
                } catch (error) {
                    console.error('Gagal fetch riwayat:', error);
                    riwayatTableBody.innerHTML = `<tr><td colspan="6" class="text-center p-4 text-red-500">Gagal memuat data.</td></tr>`;
                }
            }

            btnBukaRiwayat.addEventListener('click', () => {
                historyModal.classList.remove('hidden');
                fetchRiwayat();
            });
            closeHistoryModal.addEventListener('click', () => {
                historyModal.classList.add('hidden');
            });
            historyModal.addEventListener('click', (event) => {
                if (event.target === historyModal) {
                    historyModal.classList.add('hidden');
                }
            });

            btnBukaPeta.addEventListener('click', () => {
                mapModal.classList.remove('hidden');
            });
            closeMapModal.addEventListener('click', () => {
                mapModal.classList.add('hidden');
            });
            mapModal.addEventListener('click', (event) => {
                if (event.target === mapModal) {
                    mapModal.classList.add('hidden');
                }
            });


            populateSelectors();
            selectBulan.addEventListener('change', fetchRiwayat);
            selectTahun.addEventListener('change', fetchRiwayat);
        });

        function showIzinModal() {
            document.getElementById('izinModal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
        document.getElementById('izinModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal('izinModal');
            }
        });
    </script>
</body>

</html>
