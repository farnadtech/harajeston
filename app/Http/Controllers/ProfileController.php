<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'              => 'required|string|max:100',
            'email'             => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'             => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'bio'               => 'nullable|string|max:500',
            'avatar'            => 'nullable|image|max:2048',
            'password'          => 'nullable|string|min:8|confirmed',
        ]);

        $updateData = [
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'bio'   => $request->bio,
        ];

        // If email changed, revoke email verification
        if ($request->email !== $user->email) {
            $updateData['email_verified_at'] = null;
        }

        // If phone changed, revoke phone verification
        if ($request->phone !== $user->phone) {
            $updateData['phone_verified_at'] = null;
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $file = $request->file('avatar');
            $path = 'avatars/' . uniqid() . '.' . $file->getClientOriginalExtension();
            $updateData['avatar'] = $this->resizeAndSaveAvatar($file, $path);
        }

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        $message = 'پروفایل با موفقیت بروزرسانی شد.';
        if (isset($updateData['email_verified_at']) || isset($updateData['phone_verified_at'])) {
            $message .= ' لطفاً اطلاعات تماس جدید خود را مجدداً تأیید کنید.';
        }

        return back()->with('success', $message);
    }

    private function resizeAndSaveAvatar($file, string $path): string
    {
        $size = 100; // fixed square avatar size
        $mime = $file->getMimeType();
        $tmpPath = $file->getRealPath();

        Storage::disk('public')->makeDirectory('avatars');

        if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
            $src = imagecreatefromjpeg($tmpPath);
        } elseif ($mime === 'image/png') {
            $src = imagecreatefrompng($tmpPath);
        } elseif ($mime === 'image/gif') {
            $src = imagecreatefromgif($tmpPath);
        } elseif ($mime === 'image/webp') {
            $src = imagecreatefromwebp($tmpPath);
        } else {
            $file->storeAs('avatars', basename($path), 'public');
            return $path;
        }

        $w = imagesx($src);
        $h = imagesy($src);

        // Crop to square from center, then resize to $size x $size
        $minDim = min($w, $h);
        $srcX = (int)(($w - $minDim) / 2);
        $srcY = (int)(($h - $minDim) / 2);

        $dst = imagecreatetruecolor($size, $size);
        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $size, $size, $minDim, $minDim);

        $finalPath = 'avatars/' . uniqid() . '.jpg';
        $savePath = Storage::disk('public')->path($finalPath);
        imagejpeg($dst, $savePath, 85);

        imagedestroy($src);
        imagedestroy($dst);

        return $finalPath;
    }
}
