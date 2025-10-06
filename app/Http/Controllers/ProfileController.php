<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the admin profile page
     */
    public function index()
    {
        return view('admin.profile');
    }

    /**
     * Update admin profile information
     */
    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:admin,username,' . $admin->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admin,email,' . $admin->id],
        ], [
            'name.required' => 'Nama harus diisi',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
        ]);

        $admin->update($validated);

        return redirect()->route('admin.profile')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update admin password
     */
    public function updatePassword(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini harus diisi',
            'password.required' => 'Password baru harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // Verify current password
        if (!Hash::check($validated['current_password'], $admin->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai'
            ])->withInput();
        }

        // Update password
        $admin->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()->route('admin.profile')
            ->with('success', 'Password berhasil diubah!');
    }

    /**
     * Logout admin
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Anda berhasil logout!');
    }
}