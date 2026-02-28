<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = User::query()->where('role', '<>', 'admin');

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('active')) {
            $query->where('active', $request->input('active'));
        }

        if ($request->has('trashed')) {
            $query->withTrashed();
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        /** @var \App\Models\User $user */
        $user = User::withTrashed()->find($id);
        if (!$user) {
            return Redirect::route('admin.users.index')->with('error', 'User not found');
        }

        $orders = DB::table('orders')
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users.show', [
            'user' => $user,
            'orders' => $orders
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = User::withTrashed()->find($id);
        if (!$user || $user->role === 'admin') {
            return Redirect::route('admin.users.index')->with('error', 'Invalid user');
        }

        $validated = $request->validate([
            'active' => 'required|boolean'
        ]);

        $user->active = $validated['active'];
        $user->save();

        return Redirect::route('admin.users.show', $id)->with('success', 'User status updated successfully');
    }

    public function edit($id)
    {
        /** @var \App\Models\User $user */
        $user = User::withTrashed()->findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = User::withTrashed()->findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->getKey(),
            'role' => 'required|in:admin,user',
            'active' => 'required|boolean',
        ]);

        if ((int) auth()->id() === (int) $user->getKey() && !$data['active']) {
            return redirect()->back()->with('status','You cannot deactivate your own account.');
        }

        $user->update($data);
        return redirect()->route('admin.users.index')->with('status', 'User updated.');
    }

    public function destroy($id)
    {
        /** @var \App\Models\User $user */
        $user = User::findOrFail($id);
        if ((int) auth()->id() === (int) $id) {
            return redirect()->back()->with('status', 'Cannot delete yourself.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('status', 'User deleted.');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return redirect()->route('admin.users.index')->with('status', 'User restored.');
    }
}
