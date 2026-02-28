<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\UserImage;

class UserProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit()
    {
        $user = Auth::user();
        return view('user.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|confirmed|min:8',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            // when uploading files we expect an array. "sometimes" is a little
            // more tolerant: if the input isn't present the rule won't fire, and
            // if the browser only sends a single file we still want to treat it
            // as an array (thanks to the [] name).
            'images' => 'sometimes|array',
            'images.*' => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        // handle multiple uploads reliably
        $files = $request->file('images') ?: [];
        // flash the number of uploaded files so developers/users can debug
        $count = is_array($files) ? count($files) : ($files ? 1 : 0);
        session()->flash('files_count', $count);

        foreach ((array) $files as $img) {
            if ($img && $img->isValid()) {
                $path = $img->store('user_images', 'public');
                $user->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('user.profile.edit')->with('status','Profile updated.');
    }

    public function deleteImage($id)
    {
        $img = UserImage::findOrFail($id);
        if ($img->user_id !== Auth::id()) {
            abort(403);
        }
        Storage::disk('public')->delete($img->path);
        $img->delete();
        return redirect()->route('user.profile.edit')->with('status','Image removed.');
    }
}
