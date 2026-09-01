<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Tampilkan halaman profil
    public function index()
    {
        $profile = auth()->user()->profile;
        return view('profile.index', compact('profile'));
    }

    // Simpan atau update profil
    public function update(Request $request)
    {
        $request->validate([
            'front_title'   => 'nullable|string|max:50',
            'back_title'    => 'nullable|string|max:100',
            'nip'           => 'nullable|string|max:50',
            'nidn'          => 'nullable|string|max:50',
            'phone'         => 'nullable|string|max:20',
            'study_program' => 'nullable|string|max:100',
            'position'      => 'nullable|string|max:100',
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'institution_name'    => 'nullable|string|max:255',
            'institution_address' => 'nullable|string',
            'institution_email'   => 'nullable|email|max:255',
            'institution_website' => 'nullable|string|max:255',
            'institution_phone'   => 'nullable|string|max:20',
            'institution_logo'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only([
            'front_title', 'back_title', 'nip', 'nidn',
            'phone', 'study_program', 'position',
            'institution_name', 'institution_address',
            'institution_email', 'institution_website', 'institution_phone',
        ]);

        // Upload foto jika ada
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            $oldProfile = auth()->user()->profile;
            if ($oldProfile && $oldProfile->photo) {
                Storage::disk('public')->delete($oldProfile->photo);
            }
            $data['photo'] = $request->file('photo')->store('photos', 'public');
            auth()->user()->addStorageUsed($request->file('photo')->getSize());
        }
        // Upload logo kampus jika ada
        if ($request->hasFile('institution_logo')) {
            $oldProfile = auth()->user()->profile;
            if ($oldProfile && $oldProfile->institution_logo) {
                Storage::disk('public')->delete($oldProfile->institution_logo);
            }
            $data['institution_logo'] = $request->file('institution_logo')
                                               ->store('logos', 'public');
            auth()->user()->addStorageUsed($request->file('institution_logo')->getSize());
        }

        // Update atau create profil
        Profile::updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        return redirect()->route('profile.index')
                        ->with('success', 'Profil berhasil disimpan!');
    }

    // Hapus foto profil
    public function deletePhoto()
    {
        $profile = auth()->user()->profile;
        if ($profile && $profile->photo) {
            Storage::disk('public')->delete($profile->photo);
            $fileSize = Storage::disk('public')->exists($profile->photo)
                       ? Storage::disk('public')->size($profile->photo) : 0;
            auth()->user()->reduceStorageUsed($fileSize);
            $profile->update(['photo' => null]);
        }

        return redirect()->route('profile.index')
                        ->with('success', 'Foto profil berhasil dihapus!');
    }
    // Halaman ganti password
    public function changePassword()
    {
        return view('profile.change-password');
    }

    // Proses ganti password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'new_password.required'     => 'Password baru wajib diisi.',
            'new_password.min'          => 'Password baru minimal 8 karakter.',
            'new_password.confirmed'    => 'Konfirmasi password tidak cocok.',
        ]);

        // Cek password lama
        if (!\Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors([
                'current_password' => 'Password lama tidak sesuai!'
            ]);
        }

        // Update password
        auth()->user()->update([
            'password' => \Hash::make($request->new_password)
        ]);

        return redirect()->route('profile.index')
                        ->with('success', 'Password berhasil diubah! Silakan login kembali.');
    }
}