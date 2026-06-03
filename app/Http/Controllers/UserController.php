<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{

    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('kullanicilar.index', compact('users'));
    }


    public function create()
    {
        return view('kullanicilar.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:admin,personel'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'permissions' => [],
        ]);

        return redirect()->route('users.index')->with('success', 'Kullanıcı başarıyla oluşturuldu. Şimdi yetkilerini düzenleyebilirsiniz.');
    }

    public function edit(User $user)
    {
        return view('kullanicilar.edit', compact('user'));
    }


    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class . ',email,' . $user->id],
            'role' => ['required', 'string', 'in:admin,personel'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Kullanıcı bilgileri güncellendi.');
    }


    public function permissions(User $user)
    {
        return view('kullanicilar.permissions', compact('user'));
    }


    public function updatePermissions(Request $request, User $user)
    {
        $user->permissions = $request->permissions ?? [];
        $user->save();

        return redirect()->route('users.index')->with('success', $user->name . ' kullanıcısının yetkileri güncellendi.');
    }


    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'Kendi hesabınızı silemezsiniz.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Kullanıcı başarıyla silindi.');
    }
}