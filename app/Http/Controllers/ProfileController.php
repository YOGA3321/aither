<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'NoTlp' => 'nullable|string|max:20',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->NoTlp = $request->NoTlp;
        $user->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function password(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Password berhasil diganti!');
    }
}
