<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    /** Kullanıcı mesajını kaydet */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|string',
            'oncelik'  => 'required|string',
            'mesaj'    => 'required|string',
        ]);

        SupportMessage::create([
            'user_id'  => Auth::id(),
            'kategori' => $validated['kategori'],
            'oncelik'  => $validated['oncelik'],
            'mesaj'    => $validated['mesaj'],
        ]);

        return response()->json(['success' => true]);
    }

    /** Admin mesaj listesi */
    public function index()
    {
        // Okunmamış kullanıcı cevaplarını okundu olarak işaretle
        \App\Models\SupportReply::where('is_read', false)
            ->whereHas('user', function($q) { $q->where('role', '!=', 'admin'); })
            ->update(['is_read' => true]);

        // Sadece admin erişebilir (middleware ile korunacak)
        $messages = SupportMessage::with(['user', 'replies.user'])->latest()->paginate(20);
        return view('help.admin_messages', compact('messages'));
    }

    /** Mesaj detayını oku / durumu güncelle */
    public function updateStatus(Request $request, $id)
    {
        $message = SupportMessage::findOrFail($id);
        $message->update([
            'durum' => $request->durum ?? $message->durum,
            'admin_notu' => $request->admin_notu ?? $message->admin_notu,
        ]);

        return response()->json(['success' => true]);
    }

    /** Kullanıcının kendi mesajlarını görmesi */
    public function indexUser()
    {
        // Okunmamış admin cevaplarını (bu kullanıcıya ait biletlerdeki) okundu işaretle
        \App\Models\SupportReply::where('is_read', false)
            ->whereHas('user', function($q) { $q->where('role', 'admin'); })
            ->whereHas('supportMessage', function($q) { $q->where('user_id', Auth::id()); })
            ->update(['is_read' => true]);

        $messages = SupportMessage::where('user_id', Auth::id())->with('replies.user')->latest()->paginate(15);
        return view('help.user_messages', compact('messages'));
    }

    /** Cevap yazma (Hem kullanıcı hem admin için ortaktır) */
    public function storeReply(Request $request, $id)
    {
        $message = SupportMessage::findOrFail($id);
        
        // Eğer kullanıcı admin değilse ve mesaj kendisine ait değilse hata ver
        if (Auth::user()->role !== 'admin' && $message->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'mesaj' => 'required|string',
        ]);

        \App\Models\SupportReply::create([
            'support_message_id' => $message->id,
            'user_id'            => Auth::id(),
            'mesaj'              => $validated['mesaj'],
        ]);

        // Opsiyonel: Admin cevap yazdıysa durumu 'okundu' veya 'cozuldu' gibi güncelleyebiliriz? 
        // Şimdilik sadece mesaj ekliyoruz.

        return response()->json(['success' => true]);
    }
}
