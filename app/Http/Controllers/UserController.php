<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    private function ensureAdmin()
    {
        abort_if(Auth::user()->role !== 'admin', 403, 'Unauthorized');
    }
    // ADD USER 
    public function create()
    {
        $this->ensureAdmin();
        return view('admin.users.create');
    }

    //SAVE NEW USER
    public function store(Request $request)
    {
        $this->ensureAdmin();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,participant'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }
    // LIST USER
    public function index()
    {
        $this->ensureAdmin();
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    // APPROVE / ACTIVATE
    public function approve(User $user)
    {
        $this->ensureAdmin();
        $user->update(['is_active' => true]);
        return back()->with('success', 'User approved.');
    }

    // DEACTIVATE
    public function deactivate(User $user)
    {
        $this->ensureAdmin();
        $user->update(['is_active' => false]);
        return back()->with('success', 'User deactivated.');
    }

    // CHANGE ROLE
    public function updateRole(Request $request, User $user)
    {   
        $this->ensureAdmin();
        $request->validate([
            'role' => 'required|in:admin,participant'
        ]);

        $user->update(['role' => $request->role]);
        return back()->with('success', 'Role updated.');
    }
    //HAPUS USER
    public function destroy(User $user)
    {
        $this->ensureAdmin();
        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }
    
}
