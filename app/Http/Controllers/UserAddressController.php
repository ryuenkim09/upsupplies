<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserAddress;

class UserAddressController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $addresses = $user->addresses()->orderBy('created_at', 'desc')->get();
        return view('user.addresses.index', compact('addresses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => ['nullable','string','max:255'],
            'address' => ['required','string','max:2000'],
            'phone' => ['nullable','string','max:50'],
        ]);

        $addr = UserAddress::create([
            'user_id' => Auth::id(),
            'label' => $request->input('label'),
            'address' => $request->input('address'),
            'phone' => $request->input('phone'),
        ]);

        return redirect()->back()->with('success', 'Address saved.');
    }

    public function destroy(UserAddress $address)
    {
        // ensure ownership
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }
        $address->delete();
        return redirect()->back()->with('success', 'Address removed.');
    }
}
