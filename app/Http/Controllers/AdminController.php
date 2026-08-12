<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SiswaProfile;
use App\Models\PegawaiProfile;
use App\Models\Gedung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Helpers\LogHelper;
use App\Models\Setting;
use App\Models\JenisIzin;   

class AdminController extends Controller
{
    public function index()
    {
        $siswaUsers = User::with('siswaProfile.gedung')->where('role', 'siswa')->get();
        $pegawaiUsers = User::with('pegawaiProfile.gedung')->where('role', 'pegawai')->get();
        $adminUsers = User::where('role', 'admin')->get();
        $gedungs = Gedung::all();
        $jamTelat = Setting::where('key', 'jam_telat')->value('value') ?? '08:16';
        $guruUsers = User::with('siswaYangDiawasi')->where('role', 'guru')->get();
        $allSiswas = SiswaProfile::orderBy('nama_lengkap')->get();
        $jenisIzin = JenisIzin::all();


        return view('admin.dashboard', compact('siswaUsers', 'pegawaiUsers', 'allSiswas', 'guruUsers', 'adminUsers', 'gedungs', 'jamTelat', 'jenisIzin'));
    }

    public function users()
    {
        $users = User::with(['siswaProfile', 'pegawaiProfile'])->get();
        return view('admin.users', compact('users'));
    }

    public function createUser()
    {
        $gedungs = Gedung::all();
        return view('admin.create-user', compact('gedungs'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:siswa,pegawai,admin,guru',
            'nama_lengkap_siswa' => 'required_if:role,siswa|string|max:255',
            'asal_pkl' => 'required_if:role,siswa|string|max:255',
            'gedung_id_siswa' => 'required_if:role,siswa|exists:gedungs,id',
            'nama_lengkap_pegawai' => 'required_if:role,pegawai|string|max:255',
            'gedung_id_pegawai' => 'required_if:role,pegawai|exists:gedungs,id',
            'name' => 'required_if:role,guru|string|max:255',
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'integer|exists:siswa_profiles,id',
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        if ($request->role === 'guru') {
            $userData['name'] = $request->name;
        }

        if ($request->role === 'siswa') {
            SiswaProfile::create([
                'user_id' => $user->id,
                'nama_lengkap' => $request->nama_lengkap_siswa,
                'asal_pkl' => $request->asal_pkl,
                'gedung_id' => $request->gedung_id_siswa,
            ]);
        } else if ($request->role === 'pegawai') {
            PegawaiProfile::create([
                'user_id' => $user->id,
                'nama_lengkap' => $request->nama_lengkap_pegawai,
                'gedung_id' => $request->gedung_id_pegawai,
            ]);
        } else if ($request->role === 'guru') {
            $user->siswaYangDiawasi()->sync($request->input('siswa_ids', []));
        }

        LogHelper::log($request, 'Tambah User', 'Admin menambahkan user baru dengan username: ' . $user->username);

        return redirect()->route('admin.dashboard')->with('success', 'User berhasil ditambahkan');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'nama_lengkap' => ['required_if:role,siswa,pegawai', 'string', 'max:255'],
            'asal_pkl' => ['required_if:role,siswa', 'string', 'max:255'],
            'gedung_id' => ['required_if:role,siswa,pegawai', 'exists:gedungs,id'],
        ]);

        $user->username = $request->username;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        if ($user->role === 'siswa') {
            $user->siswaProfile->update([
                'nama_lengkap' => $request->nama_lengkap,
                'asal_pkl' => $request->asal_pkl,
                'gedung_id' => $request->gedung_id,
            ]);
        } else if ($user->role === 'pegawai') {
            $user->pegawaiProfile->update([
                'nama_lengkap' => $request->nama_lengkap,
                'gedung_id' => $request->gedung_id,
            ]);
        }

        LogHelper::log($request, 'Update User', 'Admin memperbarui user: ' . $user->username);

        return redirect()->route('admin.dashboard')->with('success', 'User berhasil diperbarui.');
    }


    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();


        LogHelper::log(request(), 'Hapus User', 'Admin menghapus user: ' . $user->username);

        return redirect()->route('admin.dashboard')->with('success', 'User berhasil dihapus.');
    }

    public function storeGedung(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meter' => 'required|integer|min:1',
        ]);

        Gedung::create($request->all());

        LogHelper::log($request, 'Tambah Gedung', 'Admin menambahkan gedung: ' . $request->nama);

        return redirect()->route('admin.dashboard')->with('success', 'Gedung berhasil ditambahkan');
    }

    public function updateGedung(Request $request, $id)
    {
        $gedung = Gedung::findOrFail($id);
        $request->validate([
            'nama' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meter' => 'required|integer|min:1',
        ]);

        $gedung->update($request->all());

        LogHelper::log($request, 'Update Gedung', 'Admin memperbarui gedung: ' . $gedung->nama);

        return redirect()->route('admin.dashboard')->with('success', 'Gedung berhasil diperbarui.');
    }

    public function destroyGedung($id)
    {
        $gedung = Gedung::findOrFail($id);
        $gedung->delete();

        LogHelper::log(request(), 'Hapus Gedung', 'Admin menghapus gedung: ' . $gedung->nama);

        return redirect()->route('admin.dashboard')->with('success', 'Gedung berhasil dihapus.');
    }

    public function updateJamTelat(Request $request)
    {
        $request->validate([
            'jam_telat' => 'required|date_format:H:i',
        ]);

        Setting::updateOrCreate(
            ['key' => 'jam_telat'],
            ['value' => $request->jam_telat]
        );

        LogHelper::log($request, 'Update Jam Telat', 'Admin memperbarui jam telat menjadi: ' . $request->jam_telat);

        return back()->with('success', 'Jam telat berhasil diperbarui.');
    }

    public function createGuru()
    {
        $siswas = SiswaProfile::all();
        return view('admin.guru_create', compact('siswas'));
    }

    public function storeGuru(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'integer|exists:siswa_profiles,id',
        ]);

        $guru = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'guru',
        ]);

        $guru->siswaYangDiawasi()->sync($request->input('siswa_ids', []));

        return redirect()->route('admin.guru.create')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function editGuru($id)
    {
        $guru = User::with('siswaYangDiawasi')->findOrFail($id);
        $siswas = SiswaProfile::all();
        $assignedSiswaIds = $guru->siswaYangDiawasi->pluck('id')->toArray();

        return view('admin.guru_edit', compact('guru', 'siswas', 'assignedSiswaIds'));
    }

    public function updateGuru(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username,' . $id,
            'password' => 'nullable|string|min:6',
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'integer|exists:siswa_profiles,id',
        ]);

        $guru = User::findOrFail($id);
        $guru->username = $request->username;
        if ($request->filled('password')) {
            $guru->password = Hash::make($request->password);
        }
        $guru->save();

        $guru->siswaYangDiawasi()->sync($request->input('siswa_ids', []));

        LogHelper::log($request, 'Update Guru', 'Admin memperbarui guru: ' . $guru->username);

        return redirect()->route('admin.dashboard')->with('success', 'Data guru berhasil diperbarui.');
    }
}
