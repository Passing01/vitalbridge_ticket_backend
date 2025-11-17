<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class HealthCenterController extends Controller
{
    public function index(): View
    {
        $healthCenters = User::where('role', 'reception')
            ->orderBy('first_name')
            ->get();

        return view('admin.health_centers.index', [
            'healthCenters' => $healthCenters,
        ]);
    }

    public function show(string $id): View
    {
        $healthCenter = User::where('id', $id)
            ->where('role', 'reception')
            ->with('managedDepartments')
            ->firstOrFail();

        return view('admin.health_centers.show', [
            'healthCenter' => $healthCenter,
        ]);
    }

    public function toggleActive(string $id): RedirectResponse
    {
        $healthCenter = User::where('id', $id)
            ->where('role', 'reception')
            ->firstOrFail();

        $healthCenter->is_active = !$healthCenter->is_active;
        $healthCenter->save();

        return back();
    }

    public function updatePassword(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $healthCenter = User::where('id', $id)
            ->where('role', 'reception')
            ->firstOrFail();

        $healthCenter->password = Hash::make($request->input('password'));
        $healthCenter->save();

        return back();
    }
}
