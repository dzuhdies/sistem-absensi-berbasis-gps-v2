<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .modal.hidden {
            display: none;
        }

        .dropdown-menu.hidden {
            display: none;
        }

        @media screen and (max-width: 768px) {
            .responsive-table thead {
                display: none;
            }

            .responsive-table tr {
                display: block;
                border-bottom: 2px solid #e5e7eb;
                margin-bottom: 1rem;
                background-color: #fff;
                border-radius: 0.5rem;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                padding: 0.5rem;
            }

            .responsive-table td {
                display: block;
                text-align: right;
                padding-left: 50%;
                position: relative;
                border-bottom: 1px dotted #ccc;
                padding-top: 0.75rem;
                padding-bottom: 0.75rem;
            }

            .responsive-table tr:last-child {
                border-bottom: 0;
            }

            .responsive-table td:last-child {
                border-bottom: 0;
            }

            .responsive-table td::before {
                content: attr(data-label);
                position: absolute;
                left: 0.75rem;
                width: 45%;
                padding-right: 0.75rem;
                text-align: left;
                font-weight: 600;
                color: #1f2937;
            }

            .responsive-table td.actions-cell {
                text-align: left;
                padding-left: 0.75rem;
                border-bottom: 0;
            }

            .responsive-table td.actions-cell::before {
                display: none;
            }
        }
    </style>
    @include('partials.pwa')
</head>

<body class="bg-gray-100 font-sans">

    <aside id="sidebar" class="bg-gray-800 text-white w-64 min-h-screen p-4 fixed transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-30">

        <nav class="flex flex-col gap-4">
            <div>
                <span class="text-gray-400 font-semibold text-sm px-4">MASTER USER</span>
                <a href="javascript:void(0)" onclick="showUserTable('siswa'); closeSidebar();" class="flex items-center gap-3 text-white hover:bg-gray-700 px-4 py-2 rounded-lg mt-2">
                    <i class="fas fa-user-graduate w-5"></i>
                    <span>Siswa</span>
                </a>
                <a href="javascript:void(0)" onclick="showUserTable('pegawai'); closeSidebar();" class="flex items-center gap-3 text-white hover:bg-gray-700 px-4 py-2 rounded-lg">
                    <i class="fas fa-user-tie w-5"></i>
                    <span>Pegawai</span>
                </a>
                <a href="javascript:void(0)" onclick="showUserTable('guru'); closeSidebar();" class="flex items-center gap-3 text-white hover:bg-gray-700 px-4 py-2 rounded-lg">
                    <i class="fas fa-chalkboard-teacher w-5"></i>
                    <span>Guru</span>
                </a>
                <a href="javascript:void(0)" onclick="showUserTable('admin'); closeSidebar();" class="flex items-center gap-3 text-white hover:bg-gray-700 px-4 py-2 rounded-lg">
                    <i class="fas fa-user-shield w-5"></i>
                    <span>Admin</span>
                </a>
            </div>

            <div>
                <span class="text-gray-400 font-semibold text-sm px-4">MASTER DATA</span>
                <a href="javascript:void(0)" onclick="showMainContent('gedung'); closeSidebar();" class="flex items-center gap-3 text-white hover:bg-gray-700 px-4 py-2 rounded-lg mt-2">
                    <i class="fas fa-building w-5"></i>
                    <span>Manajemen Gedung</span>
                </a>
            </div>

            <div>
                <span class="text-gray-400 font-semibold text-sm px-4">SETTINGS</span>
                <a href="javascript:void(0)" onclick="showModal('jamMasukModal'); closeSidebar();" class="flex items-center gap-3 text-white hover:bg-gray-700 px-4 py-2 rounded-lg mt-2">
                    <i class="fas fa-clock w-5"></i>
                    <span>Jam Masuk</span>
                </a>
                <a href="javascript:void(0)" onclick="showModal('jenisIzinModal'); closeSidebar();" class="flex items-center gap-3 text-white hover:bg-gray-700 px-4 py-2 rounded-lg">
                    <i class="fas fa-calendar-check w-5"></i>
                    <span>Manajemen Izin</span>
                </a>
            </div>

            <div>
                <span class="text-gray-400 font-semibold text-sm px-4">LAINNYA</span>
                <a href="{{ route('admin.logs') }}" class="flex items-center gap-3 text-white hover:bg-gray-700 px-4 py-2 rounded-lg mt-2">
                    <i class="fas fa-clipboard-list w-5"></i>
                    <span>Lihat Log</span>
                </a>
            </div>

        </nav>
    </aside>
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black bg-opacity-50 hidden md:hidden z-20"></div>

    <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300 ease-in-out">
        <header class="bg-white shadow-md p-4 flex justify-between items-center sticky top-0 z-10">
            <div class="flex items-center gap-4">
                <button id="burger-icon" class="md:hidden text-gray-800" onclick="toggleSidebar()">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Dashboard Admin</h1>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-lg shadow">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="hidden sm:inline">Logout</span>
                </button>
            </form>
        </header>
        <div class="container mx-auto p-4 sm:p-6 lg:p-8">

            @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow" role="alert">
                <p class="font-bold">Sukses</p>
                <p>{{ session('success') }}</p>
            </div>
            @endif
            @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow" role="alert">
                <p class="font-bold">Gagal</p>
                <p>{{ session('error') }}</p>
            </div>
            @endif
            @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow" role="alert">
                <p class="font-bold">Terjadi Kesalahan</p>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <main>
                <div id="main-content-user" class="main-content-view">
                    <div class="bg-white p-4 sm:p-6 rounded-lg shadow-md">
                        <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                            <h2 class="text-xl font-semibold text-gray-700 mb-4 sm:mb-0">Master User</h2>
                            <button onclick="showModal('userModal')" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">+ Tambah User</button>
                        </div>

                        <div id="user-table-siswa" class="user-table mb-8">
                            <h3 class="text-lg font-medium text-gray-600 mb-3">Data Siswa</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500 responsive-table">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">Username</th>
                                            <th scope="col" class="px-6 py-3">Nama</th>
                                            <th scope="col" class="px-6 py-3">Asal PKL</th>
                                            <th scope="col" class="px-6 py-3">Gedung</th>
                                            <th scope="col" class="px-6 py-3">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($siswaUsers as $user)
                                        <tr>
                                            <td data-label="Username" class="px-6 py-3">{{ $user->username }}</td>
                                            <td data-label="Nama" class="px-6 py-3">{{ $user->siswaProfile->nama_lengkap ?? '-' }}</td>
                                            <td data-label="Asal PKL" class="px-6 py-3">{{ $user->siswaProfile->asal_pkl ?? '-' }}</td>
                                            <td data-label="Gedung" class="px-6 py-3">{{ $user->siswaProfile->gedung->nama ?? '-' }}</td>
                                            <td data-label="Aksi" class="px-6 py-3 flex flex-col items-start gap-2 sm:flex-row sm:items-center sm:gap-3 actions-cell">
                                                <button onclick="openEditUser('{{ route('admin.users.update', $user->id) }}','{{ $user->username }}','{{ $user->role }}','{{ $user->siswaProfile->nama_lengkap ?? '' }}','{{ $user->siswaProfile->asal_pkl ?? '' }}','{{ $user->siswaProfile->gedung_id ?? '' }}')" class="text-blue-600 hover:text-blue-900 font-medium">Edit</button>
                                                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?')">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-900 font-medium">Hapus</button></form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center px-6 py-4 text-gray-400">Tidak ada data siswa.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="user-table-pegawai" class="user-table hidden mb-8">
                            <h3 class="text-lg font-medium text-gray-600 mb-3">Data Pegawai</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500 responsive-table">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">Username</th>
                                            <th scope="col" class="px-6 py-3">Nama</th>
                                            <th scope="col" class="px-6 py-3">Gedung</th>
                                            <th scope="col" class="px-6 py-3">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($pegawaiUsers as $user)
                                        <tr>
                                            <td data-label="Username" class="px-6 py-3">{{ $user->username }}</td>
                                            <td data-label="Nama" class="px-6 py-3">{{ $user->pegawaiProfile->nama_lengkap ?? '-' }}</td>
                                            <td data-label="Gedung" class="px-6 py-3">{{ $user->pegawaiProfile->gedung->nama ?? '-' }}</td>
                                            <td data-label="Aksi" class="px-6 py-3 flex flex-col items-start gap-2 sm:flex-row sm:items-center sm:gap-3 actions-cell">
                                                <button onclick="openEditUser('{{ route('admin.users.update', $user->id) }}','{{ $user->username }}','{{ $user->role }}','{{ $user->pegawaiProfile->nama_lengkap ?? '' }}','','{{ $user->pegawaiProfile->gedung_id ?? '' }}')" class="text-blue-600 hover:text-blue-900 font-medium">Edit</button>
                                                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?')">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-900 font-medium">Hapus</button></form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center px-6 py-4 text-gray-400">Tidak ada data pegawai.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="user-table-guru" class="user-table hidden mb-8">
                            <h3 class="text-lg font-medium text-gray-600 mb-3">Data Guru</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500 responsive-table">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">Username</th>
                                            <th scope="col" class="px-6 py-3">Nama</th>
                                            <th scope="col" class="px-6 py-3">Siswa</th>
                                            <th scope="col" class="px-6 py-3">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($guruUsers as $user)
                                        <tr>
                                            <td data-label="Username" class="px-6 py-3">{{ $user->username }}</td>
                                            <td data-label="Nama" class="px-6 py-3">{{ $user->name ?? '-' }}</td>
                                            <td data-label="Siswa Diawasi" class="px-6 py-3">
                                                @forelse($user->siswaYangDiawasi as $siswa)
                                                <span class="inline-block bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full">{{ $siswa->nama_lengkap }}</span>
                                                @empty
                                                <span class="text-gray-400">Belum ada siswa</span>
                                                @endforelse
                                            </td>
                                            <td data-label="Aksi" class="px-6 py-3 flex flex-col items-start gap-2 sm:flex-row sm:items-center sm:gap-3 actions-cell">
                                                <button
                                                    data-action-url="{{ route('admin.guru.update', $user->id) }}"
                                                    data-username="{{ $user->username }}"
                                                    data-siswa-ids="{{ json_encode($user->siswaYangDiawasi->pluck('id')) }}"
                                                    onclick="openEditGuru(this)"
                                                    class="text-blue-600 hover:text-blue-900 font-medium">
                                                    Edit
                                                </button>
                                                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center px-6 py-4 text-gray-400">Tidak ada data guru.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="user-table-admin" class="user-table hidden">
                            <h3 class="text-lg font-medium text-gray-600 mb-3">Data Admin</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500 responsive-table">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">Username</th>
                                            <th scope="col" class="px-6 py-3">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($adminUsers as $user)
                                        <tr>
                                            <td data-label="Username" class="px-6 py-3">{{ $user->username }}</td>
                                            <td data-label="Aksi" class="px-6 py-3 actions-cell">
                                                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?')">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-900 font-medium">Hapus</button></form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="2" class="text-center px-6 py-4 text-gray-400">Tidak ada data admin.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="main-content-gedung" class="main-content-view hidden">
                    <div class="bg-white p-4 sm:p-6 rounded-lg shadow-md">
                        <div class="flex flex-col sm:flex-row justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold text-gray-700 mb-4 sm:mb-0">Manajemen Gedung</h2>
                            <button onclick="showModal('gedungModal')" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">+ Tambah Gedung</button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 responsive-table">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Nama</th>
                                        <th scope="col" class="px-6 py-3">Radius (m)</th>
                                        <th scope="col" class="px-6 py-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($gedungs as $g)
                                    <tr>
                                        <td data-label="Nama" class="px-6 py-3 font-medium text-gray-900">{{ $g->nama }}</td>
                                        <td data-label="Radius (m)" class="px-6 py-3 font-medium text-gray-900">{{ $g->radius_meter }}</td>
                                        <td data-label="Aksi" class="px-6 py-3 flex flex-col items-start gap-2 sm:flex-row sm:items-center sm:gap-3 actions-cell">
                                            <button onclick="openEditGedung('{{ $g->id }}','{{ $g->nama }}','{{ $g->latitude }}','{{ $g->longitude }}','{{ $g->radius_meter }}','{{ route('admin.gedungs.update', $g->id) }}')" class="text-blue-600 hover:text-blue-900 font-medium">Edit</button>
                                            <form method="POST" action="{{ route('admin.gedungs.destroy', $g->id) }}" onsubmit="return confirm('Yakin ingin menghapus gedung ini?')">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-900 font-medium">Hapus</button></form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center px-6 py-4 text-gray-400">Tidak ada data gedung.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <div id="jamMasukModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="modal-content bg-white p-6 rounded-lg shadow-xl w-full max-w-sm">
            <h3 class="text-xl font-semibold mb-4">Pengaturan Jam Masuk</h3>
            <form method="POST" action="{{ route('admin.jam_telat.update') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jam Dinyatakan Telat</label>
                    <p class="text-xs text-gray-500 mb-2">Presensi setelah jam ini akan ditandai sebagai 'Telat'.</p>
                    <input type="time" name="jam_telat" value="{{ $jamTelat ?? '08:16' }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" onclick="closeModal('jamMasukModal')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg">Tutup</button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="jenisIzinModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="modal-content bg-white p-6 rounded-lg shadow-xl w-full max-w-2xl">
            <div class="flex justify-between items-center mb-4 border-b pb-3">
                <h3 class="text-xl font-semibold">Manajemen Jenis Izin</h3>
                <button type="button" onclick="closeModal('jenisIzinModal')" class="text-gray-500 hover:text-gray-800 text-2xl leading-none">&times;</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium text-gray-600 mb-3">Tambah Baru</h3>
                    <form action="{{ route('admin.jenis_izin.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="nama_izin" class="block text-sm font-medium text-gray-700">Nama Izin</label>
                            <input type="text" name="nama" id="nama_izin" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: Sakit">
                        </div>
                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                            Simpan
                        </button>
                    </form>
                </div>

                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-600 mb-3">Daftar Jenis Izin</h3>
                    <div class="overflow-y-auto border rounded-lg max-h-64">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nama Izin</th>
                                    <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($jenisIzin as $izin)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        {{ $izin->nama }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form action="{{ route('admin.jenis_izin.destroy', $izin->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus jenis izin ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center px-6 py-4 text-gray-400">Belum ada jenis izin.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="userModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="modal-content bg-white p-6 rounded-lg shadow-xl w-full max-w-lg">
            <h3 class="text-xl font-semibold mb-4">Tambah User Baru</h3>
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="username" class="block mb-1 text-sm font-medium text-gray-700">Username</label>
                    <input type="text" name="username" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('username') }}" required>
                </div>
                <div>
                    <label for="password" class="block mb-1 text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label for="role" class="block mb-1 text-sm font-medium text-gray-700">Role</label>
                    <select name="role" onchange="toggleForm(this.value)" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="siswa" {{ old('role') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                        <option value="pegawai" {{ old('role') === 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                        <option value="guru" {{ old('role') === 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div id="formSiswa" class="hidden space-y-4 pt-4 border-t">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Nama Lengkap Siswa</label>
                        <input type="text" name="nama_lengkap_siswa" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('nama_lengkap_siswa') }}">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Asal PKL</label>
                        <input type="text" name="asal_pkl" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('asal_pkl') }}">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Gedung</label>
                        <select name="gedung_id_siswa" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Pilih Gedung --</option>
                            @foreach ($gedungs as $g)
                            <option value="{{ $g->id }}" {{ old('gedung_id_siswa') == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="formPegawai" class="hidden space-y-4 pt-4 border-t">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Nama Lengkap Pegawai</label>
                        <input type="text" name="nama_lengkap_pegawai" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('nama_lengkap_pegawai') }}">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Gedung</label>
                        <select name="gedung_id_pegawai" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Pilih Gedung --</option>
                            @foreach ($gedungs as $g)
                            <option value="{{ $g->id }}" {{ old('gedung_id_pegawai') == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="formGuru" class="hidden space-y-4 pt-4 border-t">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Nama Lengkap Guru</label>
                        <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm" value="{{ old('name') }}">
                    </div>
                    <div>
                        <label for="guru-siswa-select" class="block mb-1 text-sm font-medium text-gray-700">Pilih Siswa yang Diawasi</label>
                        <select id="guru-siswa-select" name="siswa_ids[]" multiple>
                            @foreach ($allSiswas as $siswa)
                            <option value="{{ $siswa->id }}">{{ $siswa->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" onclick="closeModal('userModal')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg">Tutup</button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="userEditModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="modal-content bg-white p-6 rounded-lg shadow-xl w-full max-w-lg">
            <h3 class="text-xl font-semibold mb-4">Edit User</h3>
            <form method="POST" id="editUserForm" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="role" id="editUserRole">
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Username</label>
                    <input type="text" name="username" id="editUserUsername" class="w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Password (Opsional)</label>
                    <input type="password" name="password" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Isi untuk mengubah password">
                </div>
                <div id="editFormSiswa" class="hidden space-y-4 pt-4 border-t">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="editSiswaNama" class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Asal PKL</label>
                        <input type="text" name="asal_pkl" id="editSiswaAsal" class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                <div id="editFormPegawai" class="hidden space-y-4 pt-4 border-t">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="editPegawaiNama" class="w-full border-gray-300 rounded-md shadow-sm" disabled>
                    </div>
                </div>
                <div id="editGedungSelection" class="hidden">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Gedung</label>
                    <select name="gedung_id" id="editUserGedung" class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Pilih Gedung --</option>
                        @foreach ($gedungs as $g)
                        <option value="{{ $g->id }}">{{ $g->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" onclick="closeModal('userEditModal')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg">Tutup</button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="gedungModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="modal-content bg-white p-6 rounded-lg shadow-xl w-full max-w-lg">
            <h3 class="text-xl font-semibold mb-4">Tambah Gedung Baru</h3>
            <form method="POST" action="{{ route('admin.gedungs.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Nama Gedung</label>
                    <input type="text" name="nama" class="w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Latitude</label>
                        <input type="text" name="latitude" class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Longitude</label>
                        <input type="text" name="longitude" class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                </div>
                <button type="button" onclick="getGPSLocation('gedungModal')" class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg">📍 Ambil Lokasi GPS Saat Ini</button>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Radius (meter)</label>
                    <input type="number" name="radius_meter" value="{{ old('radius_meter', 10) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" onclick="closeModal('gedungModal')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg">Tutup</button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="gedungEditModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="modal-content bg-white p-6 rounded-lg shadow-xl w-full max-w-lg">
            <h3 class="text-xl font-semibold mb-4">Edit Gedung</h3>
            <form method="POST" id="editGedungForm" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Nama Gedung</label>
                    <input type="text" name="nama" id="editGedungNama" class="w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Latitude</label>
                        <input type="text" name="latitude" id="editGedungLat" class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Longitude</label>
                        <input type="text" name="longitude" id="editGedungLong" class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                </div>
                <button type="button" onclick="getGPSLocation('gedungEditModal')" class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg">📍 Ambil Lokasi GPS Saat Ini</button>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Radius (meter)</label>
                    <input type="number" name="radius_meter" id="editGedungRadius" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" onclick="closeModal('gedungEditModal')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg">Tutup</button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="guruEditModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="modal-content bg-white p-6 rounded-lg shadow-xl w-full max-w-lg">
            <h3 class="text-xl font-semibold mb-4">Edit Guru</h3>
            <form method="POST" id="editGuruForm" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Username</label>
                    <input type="text" name="username" id="editGuruUsername" class="w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Password Baru (Opsional)</label>
                    <input type="password" name="password" id="editGuruPassword" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Isi hanya jika ingin mengubah password">
                </div>
                <div>
                    <label for="guru-edit-siswa-select" class="block mb-1 text-sm font-medium text-gray-700">Pilih Siswa yang Diawasi</label>
                    <select id="guru-edit-siswa-select" name="siswa_ids[]" multiple>
                        @foreach ($allSiswas as $siswa)
                        <option value="{{ $siswa->id }}" data-label="{{ $siswa->nama_lengkap }}">
                            {{ $siswa->nama_lengkap }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" onclick="closeModal('guruEditModal')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg">Tutup</button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        let choicesAdd = null;
        let choicesEdit = null;

        function showMainContent(contentToShow) {
            document.querySelectorAll('.main-content-view').forEach(view => {
                view.classList.add('hidden');
            });
            const viewId = `main-content-${contentToShow}`;
            const viewToShow = document.getElementById(viewId);
            if (viewToShow) {
                viewToShow.classList.remove('hidden');
            }
            closeDropdowns();

            localStorage.setItem('dashboard_active_view', contentToShow);
            if (contentToShow !== 'user') {
                localStorage.removeItem('dashboard_active_table');
            }
        }

        function showUserTable(roleToShow) {
            showMainContent('user');
            document.querySelectorAll('.user-table').forEach(table => {
                table.classList.add('hidden');
            });
            if (roleToShow === 'semua') {
                document.querySelectorAll('.user-table').forEach(table => {
                    table.classList.remove('hidden');
                });
            } else {
                const tableId = `user-table-${roleToShow}`;
                const tableToShow = document.getElementById(tableId);
                if (tableToShow) {
                    tableToShow.classList.remove('hidden');
                }
            }

            localStorage.setItem('dashboard_active_table', roleToShow);
        }

        function showModal(modalId) {
            closeDropdowns();
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                const form = modal.querySelector('form');
                if (form) {
                    form.reset();
                }
            }
        }

        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            const isHidden = dropdown.classList.contains('hidden');
            closeDropdowns();
            if (isHidden) {
                dropdown.classList.remove('hidden');
            }
        }

        function closeDropdowns() {
            document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
                menu.classList.add('hidden');
            });
        }

        function toggleForm(role) {
            const siswaForm = document.getElementById('formSiswa');
            const pegawaiForm = document.getElementById('formPegawai');
            const guruForm = document.getElementById('formGuru');

            [siswaForm, pegawaiForm, guruForm].forEach(form => {
                if (form) {
                    form.classList.add('hidden');
                    form.querySelectorAll('input, select').forEach(el => el.disabled = true);
                }
            });

            if (role === 'siswa' && siswaForm) {
                siswaForm.classList.remove('hidden');
                siswaForm.querySelectorAll('input, select').forEach(el => el.disabled = false);
            } else if (role === 'pegawai' && pegawaiForm) {
                pegawaiForm.classList.remove('hidden');
                pegawaiForm.querySelectorAll('input, select').forEach(el => el.disabled = false);
            } else if (role === 'guru' && guruForm) {
                guruForm.classList.remove('hidden');
                guruForm.querySelectorAll('input, select').forEach(el => el.disabled = false);
                // Kosongkan pilihan aktif tanpa menghapus daftar siswa.
                if (choicesAdd) choicesAdd.removeActiveItems();
            }
        }

        function toggleEditForm(role) {
            const editSiswaForm = document.getElementById('editFormSiswa');
            const editPegawaiForm = document.getElementById('editFormPegawai');
            const gedungSelection = document.getElementById('editGedungSelection');

            [editSiswaForm, editPegawaiForm, gedungSelection].forEach(form => {
                if (form) {
                    form.classList.add('hidden');
                    form.querySelectorAll('input, select').forEach(el => el.disabled = true);
                }
            });

            if (role === 'siswa') {
                editSiswaForm.classList.remove('hidden');
                gedungSelection.classList.remove('hidden');
                editSiswaForm.querySelectorAll('input').forEach(el => el.disabled = false);
                document.getElementById('editSiswaNama').name = 'nama_lengkap';

                // TAMBAHKAN BARIS INI UNTUK MENGAKTIFKAN DROPDOWN GEDUNG
                document.getElementById('editUserGedung').disabled = false;

            } else if (role === 'pegawai') {
                editPegawaiForm.classList.remove('hidden');
                gedungSelection.classList.remove('hidden');
                const namaPegawaiInput = document.getElementById('editPegawaiNama');
                namaPegawaiInput.disabled = false;
                namaPegawaiInput.name = 'nama_lengkap';

                // TAMBAHKAN BARIS INI JUGA UNTUK PEGAWAI
                document.getElementById('editUserGedung').disabled = false;
            }
        }

        function openEditUser(actionUrl, username, role, nama_lengkap, asal_pkl, gedung_id) {
            const modal = document.getElementById('userEditModal');
            modal.querySelector('#editUserForm').action = actionUrl;
            modal.querySelector('#editUserUsername').value = username;
            modal.querySelector('#editUserRole').value = role;
            toggleEditForm(role);

            if (role === 'siswa') {
                document.getElementById('editSiswaNama').value = nama_lengkap;
                document.getElementById('editSiswaAsal').value = asal_pkl;
            } else if (role === 'pegawai') {
                document.getElementById('editPegawaiNama').value = nama_lengkap;
            }

            if (role === 'siswa' || role === 'pegawai') {
                document.getElementById('editUserGedung').value = gedung_id;
            }

            showModal('userEditModal');
        }

        function openEditGedung(id, nama, lat, long, radius, actionUrl) {
            const modal = document.getElementById('gedungEditModal');
            modal.querySelector('#editGedungNama').value = nama;
            modal.querySelector('#editGedungLat').value = lat;
            modal.querySelector('#editGedungLong').value = long;
            modal.querySelector('#editGedungRadius').value = radius;
            modal.querySelector('#editGedungForm').action = actionUrl;
            showModal('gedungEditModal');
        }

        function openEditGuru(buttonElement) {
            try {
                const actionUrl = buttonElement.getAttribute('data-action-url');
                const username = buttonElement.getAttribute('data-username');
                const assignedSiswaIds = JSON.parse(buttonElement.getAttribute('data-siswa-ids'));

                const modal = document.getElementById('guruEditModal');
                modal.querySelector('#editGuruForm').action = actionUrl;
                modal.querySelector('#editGuruUsername').value = username;
                modal.querySelector('#editGuruPassword').value = '';

                const siswaSelect = document.getElementById('guru-edit-siswa-select');

                if (choicesEdit) {
                    choicesEdit.destroy();
                }

                choicesEdit = new Choices(siswaSelect, {
                    removeItemButton: true,
                    placeholder: true,
                    placeholderValue: 'Ketik untuk mencari siswa...',
                    shouldSort: false,
                });

                choicesEdit.setChoiceByValue(assignedSiswaIds.map(String));

                showModal('guruEditModal');

            } catch (error) {
                console.error("ERROR di dalam fungsi openEditGuru:", error);
                alert("Terjadi error JavaScript. Silakan cek Developer Console untuk melihat detailnya.");
            }
        }


        function getGPSLocation(modalId) {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung Geolocation.');
                return;
            }
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const modal = document.getElementById(modalId);
                    modal.querySelector('input[name="latitude"]').value = position.coords.latitude;
                    modal.querySelector('input[name="longitude"]').value = position.coords.longitude;
                    alert('Lokasi berhasil diambil!');
                },
                function(error) {
                    alert('Gagal mengambil lokasi: ' + error.message);
                }
            );
        }

        window.onclick = function(event) {
            if (!event.target.matches('button, button *')) {
                closeDropdowns();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const addSiswaSelect = document.getElementById('guru-siswa-select');
            if (addSiswaSelect) {
                choicesAdd = new Choices(addSiswaSelect, {
                    removeItemButton: true,
                    placeholder: true,
                    placeholderValue: 'Ketik untuk mencari siswa...',
                    shouldSort: false,
                });
            }

            const editSiswaSelect = document.getElementById('guru-edit-siswa-select');
            if (editSiswaSelect) {
                choicesEdit = new Choices(editSiswaSelect, {
                    removeItemButton: true,
                    placeholder: true,
                    placeholderValue: 'Ketik untuk mencari siswa...',
                    shouldSort: false,
                });
            }

            const hasErrors = "{{ $errors->any() ? 'true' : 'false' }}" === 'true';
            if (hasErrors) {
                const oldRole = "{{ old('role') }}";
                if (oldRole) {
                    showModal('userModal');
                    toggleForm(oldRole);
                }
            }

            const savedView = localStorage.getItem('dashboard_active_view');
            const savedTable = localStorage.getItem('dashboard_active_table');

            if (savedView) {
                showMainContent(savedView);
                if (savedView === 'user' && savedTable) {
                    showUserTable(savedTable);
                }
            } else {
                showUserTable('siswa');
            }
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if (window.innerWidth < 768) { // Cek jika di mode mobile
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }
    </script>
</body>

</html>
