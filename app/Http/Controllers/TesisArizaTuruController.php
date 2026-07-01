<?php

namespace App\Http\Controllers;

use App\Models\TesisArizaTuru;
use Illuminate\Http\Request;

class TesisArizaTuruController extends Controller
{
    public function index()
    {
        $turler = TesisArizaTuru::orderBy('ad')->paginate(20);

        return view('tesis-ariza-turleri.index', compact('turler'));
    }

    public function create()
    {
        return view('tesis-ariza-turleri.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ad' => 'required|string|max:200|unique:tesis_ariza_turleri,ad',
        ]);

        TesisArizaTuru::create($validated);

        return redirect()->route('tesis-bilgi-sistemi.ariza-turleri')
            ->with('success', 'Arıza türü başarıyla eklendi.');
    }

    public function edit($id)
    {
        $tur = TesisArizaTuru::findOrFail($id);

        return view('tesis-ariza-turleri.form', compact('tur'));
    }

    public function update(Request $request, $id)
    {
        $tur = TesisArizaTuru::findOrFail($id);

        $validated = $request->validate([
            'ad' => 'required|string|max:200|unique:tesis_ariza_turleri,ad,'.$tur->id,
        ]);

        $tur->update($validated);

        return redirect()->route('tesis-bilgi-sistemi.ariza-turleri')
            ->with('success', 'Arıza türü başarıyla güncellendi.');
    }

    public function destroy($id)
    {
        $tur = TesisArizaTuru::findOrFail($id);
        $tur->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('tesis-bilgi-sistemi.ariza-turleri')
            ->with('success', 'Arıza türü başarıyla silindi.');
    }
}
