<?php

namespace App\Http\Controllers;

use App\Models\TesisEkip;
use Illuminate\Http\Request;

class TesisEkipController extends Controller
{
    public function index()
    {
        $ekips = TesisEkip::orderBy('ad')->paginate(20);

        return view('tesis-ekip.index', compact('ekips'));
    }

    public function create()
    {
        return view('tesis-ekip.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ad' => 'required|string|max:200|unique:tesis_ekipleri,ad',
        ]);

        TesisEkip::create($validated);

        return redirect()->route('tesis-bilgi-sistemi.ekip')
            ->with('success', 'Ekip başarıyla eklendi.');
    }

    public function edit($id)
    {
        $ekip = TesisEkip::findOrFail($id);

        return view('tesis-ekip.form', compact('ekip'));
    }

    public function update(Request $request, $id)
    {
        $ekip = TesisEkip::findOrFail($id);

        $validated = $request->validate([
            'ad' => 'required|string|max:200|unique:tesis_ekipleri,ad,'.$ekip->id,
        ]);

        $ekip->update($validated);

        return redirect()->route('tesis-bilgi-sistemi.ekip')
            ->with('success', 'Ekip başarıyla güncellendi.');
    }

    public function destroy($id)
    {
        $ekip = TesisEkip::findOrFail($id);
        $ekip->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('tesis-bilgi-sistemi.ekip')
            ->with('success', 'Ekip başarıyla silindi.');
    }
}
