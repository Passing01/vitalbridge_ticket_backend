<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SpecialtyController extends Controller
{
    /**
     * Afficher la liste des spécialités
     */
    public function index(Request $request)
    {
        $query = Specialty::with('department')
            ->latest();
            
        // Filtrage par département si un ID de département est fourni
        if ($request->has('department')) {
            $query->where('department_id', $request->department);
        }
        
        $specialties = $query->paginate(10);
        $departments = Department::orderBy('name')->get();
        
        return view('specialties.index', compact('specialties', 'departments'));
    }

    /**
     * Afficher le formulaire de création d'une spécialité
     */
    public function create()
    {
        $departments = Department::orderBy('name')->get();
        return view('specialties.create', compact('departments'));
    }

    /**
     * Enregistrer une nouvelle spécialité
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'specialties' => 'required|array|min:1',
            'specialties.*.name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'specialties.*.description' => 'nullable|string',
        ]);

        $createdCount = 0;
        $errors = [];

        foreach ($request->specialties as $index => $specialtyData) {
            // Vérifier si une spécialité avec le même nom existe déjà dans ce département
            $exists = Specialty::where('name', $specialtyData['name'])
                ->where('department_id', $request->department_id)
                ->exists();
                
            if ($exists) {
                $errors[] = "La spécialité '{$specialtyData['name']}' existe déjà dans ce département.";
                continue;
            }

            Specialty::create([
                'name' => $specialtyData['name'],
                'department_id' => $request->department_id,
                'description' => $specialtyData['description'] ?? null,
            ]);
            
            $createdCount++;
        }

        if ($createdCount > 0) {
            $message = $createdCount . ' spécialité' . ($createdCount > 1 ? 's ont été créées' : ' a été créée') . ' avec succès.';
            
            if (!empty($errors)) {
                $message .= ' Cependant, certaines spécialités n\'ont pas pu être créées : ' . implode(' ', $errors);
            }
            
            return redirect()->route('specialties.index', ['department' => $request->department_id])
                ->with('success', $message);
        }
        
        return back()
            ->withInput()
            ->with('error', 'Aucune spécialité n\'a pu être créée. ' . implode(' ', $errors));
    }

    /**
     * Afficher le formulaire d'édition d'une spécialité
     */
    public function edit(Specialty $specialty)
    {
        $departments = Department::orderBy('name')->get();
        return view('specialties.edit', compact('specialty', 'departments'));
    }

    /**
     * Mettre à jour une spécialité
     */
    public function update(Request $request, Specialty $specialty)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('specialties')
                    ->where('department_id', $request->department_id)
                    ->ignore($specialty->id),
            ],
            'department_id' => 'required|exists:departments,id',
            'description' => 'nullable|string',
        ]);

        $specialty->update($validated);

        return redirect()->route('specialties.index')
            ->with('success', 'Spécialité mise à jour avec succès');
    }

    /**
     * Supprimer une spécialité
     */
    public function destroy(Specialty $specialty)
    {
        // Vérifier si la spécialité est utilisée par des médecins
        if ($specialty->doctorProfiles()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer cette spécialité car elle est utilisée par un ou plusieurs médecins.');
        }

        $specialty->delete();

        return redirect()->route('specialties.index')
            ->with('success', 'Spécialité supprimée avec succès');
    }
}
