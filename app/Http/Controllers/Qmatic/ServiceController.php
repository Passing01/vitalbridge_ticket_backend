<?php

namespace App\Http\Controllers\Qmatic;

use App\Http\Controllers\Controller;
use App\Models\QmaticService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ServiceController extends Controller
{
    use AuthorizesRequests;
    /**
     * Afficher la liste des services
     */
    public function index()
    {
        $user = Auth::user();
        $healthCenterId = $user->role === 'reception' ? $user->id : $user->health_center_id;
        
        $services = QmaticService::where('health_center_id', $healthCenterId)
                                 ->orderBy('priority_order')
                                 ->orderBy('code')
                                 ->get();

        return view('qmatic.admin.services.index', compact('services'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('qmatic.admin.services.create');
    }

    /**
     * Enregistrer un nouveau service
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'image_url' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'priority_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'working_hours' => 'nullable|array',
        ]);

        $user = Auth::user();
        $healthCenterId = $user->role === 'reception' ? $user->id : $user->health_center_id;

        // Vérifier l'unicité du code pour ce centre
        $exists = QmaticService::where('health_center_id', $healthCenterId)
                               ->where('code', $validated['code'])
                               ->exists();

        if ($exists) {
            return back()->withErrors(['code' => 'Ce code de service existe déjà.'])->withInput();
        }

        // Traiter les horaires d'ouverture
        $workingHours = [];
        if ($request->has('working_hours')) {
            foreach ($request->working_hours as $day => $hours) {
                if (isset($hours['active'])) {
                    $workingHours[$day] = [
                        'start' => $hours['start'],
                        'end' => $hours['end'],
                    ];
                }
            }
        }

        $service = QmaticService::create([
            'health_center_id' => $healthCenterId,
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'icon' => $validated['icon'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'description' => $validated['description'] ?? null,
            'priority_order' => $validated['priority_order'] ?? 0,
            'is_active' => $request->has('is_active'),
            'working_hours' => !empty($workingHours) ? $workingHours : null,
        ]);

        return redirect()->route('qmatic.admin.services.index')
                        ->with('success', 'Service créé avec succès.');
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(QmaticService $service)
    {
        $this->authorize('update', $service);
        
        return view('qmatic.admin.services.edit', compact('service'));
    }

    /**
     * Mettre à jour un service
     */
    public function update(Request $request, QmaticService $service)
    {
        $this->authorize('update', $service);

        $validated = $request->validate([
            'code' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'image_url' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'priority_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'working_hours' => 'nullable|array',
        ]);

        $user = Auth::user();
        $healthCenterId = $user->role === 'reception' ? $user->id : $user->health_center_id;

        // Vérifier l'unicité du code (sauf pour le service actuel)
        $exists = QmaticService::where('health_center_id', $healthCenterId)
                               ->where('code', $validated['code'])
                               ->where('id', '!=', $service->id)
                               ->exists();

        if ($exists) {
            return back()->withErrors(['code' => 'Ce code de service existe déjà.'])->withInput();
        }

        // Traiter les horaires d'ouverture
        $workingHours = [];
        if ($request->has('working_hours')) {
            foreach ($request->working_hours as $day => $hours) {
                if (isset($hours['active'])) {
                    $workingHours[$day] = [
                        'start' => $hours['start'],
                        'end' => $hours['end'],
                    ];
                }
            }
        }

        $service->update([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'icon' => $validated['icon'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'description' => $validated['description'] ?? null,
            'priority_order' => $validated['priority_order'] ?? 0,
            'is_active' => $request->has('is_active'),
            'working_hours' => !empty($workingHours) ? $workingHours : null,
        ]);

        return redirect()->route('qmatic.admin.services.index')
                        ->with('success', 'Service mis à jour avec succès.');
    }

    /**
     * Supprimer un service
     */
    public function destroy(QmaticService $service)
    {
        $this->authorize('delete', $service);

        // Vérifier s'il y a des tickets en attente
        $hasWaitingTickets = $service->waitingTickets()->exists();

        if ($hasWaitingTickets) {
            return back()->withErrors(['error' => 'Impossible de supprimer ce service car il a des tickets en attente.']);
        }

        $service->delete();

        return redirect()->route('qmatic.admin.services.index')
                        ->with('success', 'Service supprimé avec succès.');
    }

    /**
     * Activer/désactiver un service
     */
    public function toggleStatus(QmaticService $service)
    {
        $this->authorize('update', $service);

        $service->update([
            'is_active' => !$service->is_active
        ]);

        $status = $service->is_active ? 'activé' : 'désactivé';

        return back()->with('success', "Service $status avec succès.");
    }
}
