<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search', '');

        $users = User::when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        })
            ->withCount(['bookings' => function ($q) {
                $q->where('status', 1); // Only count confirmed bookings
            }])
            ->orderByDesc('bookings_count')
            ->paginate(15);

        return view('admin.users.list', compact('users', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,manager,customer',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'password' => bcrypt($validated['password']),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::withCount(['bookings' => function ($q) {
            $q->where('status', 1);
        }])
            ->with(['bookings' => function ($q) {
                $q->orderBy('created_at', 'desc');
            }, 'bookings.hotel'])
            ->findOrFail($id);

        // Calculate total spend
        $totalSpend = $user->bookings->where('status', 1)->sum('total_amount');

        return view('admin.users.info', compact('user', 'totalSpend'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        session()->put('redirect_after_update', url()->previous());
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,manager,customer',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->to(session()->remove('redirect_after_update'))->with('success', 'User updated successfully!');
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:manager,customer',
        ]);

        $user = User::findOrFail($id);

        $user->role = $request->role;
        $user->save();

        return redirect()->back()->with('success', 'User role updated to '.ucfirst($request->role));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('success', 'User Deleted Successfully');
    }
}
