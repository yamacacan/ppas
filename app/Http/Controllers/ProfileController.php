<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', [
            'user' => auth()->user()
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:password|current_password',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_photo' => 'nullable|image|max:5048', // 1MB Max
        ]);

        $user->name = $validated['name'];
        if (isset($validated['last_name'])) {
            $user->last_name = $validated['last_name'];
        }
        $user->email = $validated['email'];

        // Handle Password Change
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        // Handle Profile Photo Upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->image_path) {
                Storage::delete('public/' . $user->image_path);
            }

            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->image_path = $path;
        }

        $user->save();

        return back()->with('success', 'Profiliniz başarıyla güncellendi.');
    }
}
