<?php

namespace App\Http\Controllers;

use App\Models\Gedung;
use Illuminate\Http\Request;

class GedungController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        Gedung::create($request->only(['nama', 'latitude', 'longitude']));
        return back()->with('success', 'Gedung berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $gedung = Gedung::findOrFail($id);
        $gedung->update($request->only(['nama', 'latitude', 'longitude']));
        return back()->with('success', 'Gedung berhasil diperbarui');
    }

    public function destroy($id)
    {
        $gedung = Gedung::findOrFail($id);
        $gedung->delete();
        return back()->with('success', 'Gedung berhasil dihapus');
    }
}
