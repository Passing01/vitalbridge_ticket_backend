<?php

namespace App\Http\Controllers\Qmatic\Admin;

use App\Http\Controllers\Controller;
use App\Models\QmaticCounter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CounterController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $healthCenterId = $user->role === 'reception' ? $user->id : $user->health_center_id;

        $counters = QmaticCounter::where('health_center_id', $healthCenterId)->get();

        return view('qmatic.admin.counters.index', compact('counters'));
    }

    public function create()
    {
        return view('qmatic.admin.counters.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $healthCenterId = $user->role === 'reception' ? $user->id : $user->health_center_id;

        $validated = $request->validate([
            'code' => 'required|string|max:10',
            'name' => 'required|string|max:255',
        ]);

        QmaticCounter::create([
            'id' => Str::uuid(),
            'health_center_id' => $healthCenterId,
            'code' => $validated['code'],
            'name' => $validated['name'],
            'is_active' => true,
        ]);

        return redirect()->route('qmatic.admin.counters.index')->with('success', 'Guichet créé avec succès.');
    }

    public function edit(QmaticCounter $counter)
    {
        return view('qmatic.admin.counters.edit', compact('counter'));
    }

    public function update(Request $request, QmaticCounter $counter)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $counter->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('qmatic.admin.counters.index')->with('success', 'Guichet mis à jour avec succès.');
    }

    public function destroy(QmaticCounter $counter)
    {
        $counter->delete();
        return redirect()->route('qmatic.admin.counters.index')->with('success', 'Guichet supprimé avec succès.');
    }
}
