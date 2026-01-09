<?php

namespace App\Http\Controllers\Qmatic\Admin;

use App\Http\Controllers\Controller;
use App\Models\QmaticUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $healthCenterId = $user->role === 'reception' ? $user->id : $user->health_center_id;

        $agents = QmaticUser::where('health_center_id', $healthCenterId)->get();

        return view('qmatic.admin.users.index', compact('agents'));
    }

    public function create()
    {
        return view('qmatic.admin.users.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $healthCenterId = $user->role === 'reception' ? $user->id : $user->health_center_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:qmatic_users,username',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:agent,admin',
        ]);

        QmaticUser::create([
            'id' => Str::uuid(),
            'health_center_id' => $healthCenterId,
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        return redirect()->route('qmatic.admin.users.index')->with('success', 'Agent créé avec succès.');
    }

    public function edit(QmaticUser $user)
    {
        return view('qmatic.admin.users.edit', compact('user'));
    }

    public function update(Request $request, QmaticUser $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:qmatic_users,username,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:agent,admin',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'role' => $validated['role'],
            'is_active' => $request->has('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('qmatic.admin.users.index')->with('success', 'Agent mis à jour avec succès.');
    }

    public function destroy(QmaticUser $user)
    {
        $user->delete();
        return redirect()->route('qmatic.admin.users.index')->with('success', 'Agent supprimé avec succès.');
    }
}
