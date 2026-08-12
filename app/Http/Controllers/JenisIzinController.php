<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisIzin;
use App\Helpers\LogHelper;

class JenisIzinController extends Controller
{
    public function index()
    {
        $jenisIzin = JenisIzin::all();
        return view('admin.jenis_izin.index', compact('jenisIzin'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:255']);
        
        $jenisIzin = JenisIzin::create($request->all());
        $logMessage = 'Jenis izin "' . $jenisIzin->nama . '" berhasil ditambahkan.';
        LogHelper::log($request, 'Tambah Jenis Izin', $logMessage);

        return back()->with('success', 'Jenis izin ditambahkan.');
    }

    public function destroy(Request $request, $id)
    {
        $jenisIzin = JenisIzin::withCount('absensis')->findOrFail($id);

        if ($jenisIzin->absensis_count > 0) {
            $logMessage = 'Gagal menghapus jenis izin "' . $jenisIzin->nama . '" karena sudah digunakan.';
            LogHelper::log($request, 'Gagal Hapus Jenis Izin', $logMessage);
            
            return back()->with('error', 'Jenis izin "' . $jenisIzin->nama . '" tidak dapat dihapus karena sudah digunakan.');
        }

        $namaIzinDihapus = $jenisIzin->nama;
        $jenisIzin->delete();

        $logMessage = 'Jenis izin "' . $namaIzinDihapus . '" berhasil dihapus.';
        LogHelper::log($request, 'Hapus Jenis Izin', $logMessage);

        return back()->with('success', 'Jenis izin berhasil dihapus.');
    }
}