<?php

namespace App\Http\Controllers;

use App\Models\Bolgeler;
use Illuminate\Http\Request;

class BolgelerController extends Controller
{
    public function index(Request $request)
    {
        $query = Bolgeler::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('bolge_adi', 'like', "%{$search}%")
                  ->orWhere('bolge_kodu', 'like', "%{$search}%");
            });
        }

        $bolgeler = $query->orderBy('bolge_kodu', 'asc')->paginate(20)->withQueryString();

        return view('bolgeler.index', compact('bolgeler'));
    }

    public function update(Request $request, $id)
    {
        $bolge = Bolgeler::findOrFail($id);
        
        $validated = $request->validate([
            'bolge_adi' => 'required|string|max:255'
        ]);

        $bolge->update($validated);

        return back()->with('success', 'Bölge adı başarıyla güncellendi.');
    }
}
