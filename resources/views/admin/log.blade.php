<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktivitas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.pwa')
</head>

<body class="bg-gray-100 text-gray-800">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h2 class="text-2xl font-bold mb-6">Log Aktivitas</h2>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-3 py-2 mb-4 bg-gray-200 text-gray-800 text-sm rounded hover:bg-gray-300">
            ← Kembali ke Dashboard
        </a>

        <form method="GET" action="{{ route('admin.logs') }}" class="mb-6 flex flex-wrap gap-4 items-end bg-white p-4 rounded shadow">
            <div>
                <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" value="{{ request('tanggal') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                <select id="role" name="role" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                    <option value="">Semua</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="pegawai" {{ request('role') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                    <option value="siswa" {{ request('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                </select>
            </div>
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700">User</label>
                <select id="user_id" name="user_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                    <option value="">Semua</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->username }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 mt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded shadow hover:bg-blue-700">
                    Filter
                </button>
                <a href="{{ route('admin.logs') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded shadow hover:bg-gray-400">
                    Clear
                </a>
            </div>
        </form>

        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Waktu</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">User</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Role</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Keterangan</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">IP</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">User Agent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i:s') }}</td>
                        <td class="px-4 py-2">{{ $log->user }}</td>
                        <td class="px-4 py-2 capitalize">{{ $log->role }}</td>
                        <td class="px-4 py-2">{{ $log->aksi }}</td>
                        <td class="px-4 py-2">{{ $log->keterangan }}</td>
                        <td class="px-4 py-2">{{ $log->ip_address }}</td>
                        <td class="px-4 py-2">{{ $log->user_agent }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-center text-gray-500">Belum ada log.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $logs->appends(request()->query())->links() }}
        </div>
    </div>

</body>
</html>
