<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    /**
     * Afficher la liste des départements
     */
    public function index()
    {
        $departments = Department::withCount('specialties')->latest()->paginate(10);
        return view('departments.index', compact('departments'));
    }

    /**
     * Afficher le formulaire de création d'un département
     */
    public function create()
    {
        return view('departments.create');
    }

    /**
     * Enregistrer un nouveau département
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'description' => 'nullable|string',
        ]);

        Department::create($validated);

        return redirect()->route('departments.index')
            ->with('success', 'Département créé avec succès');
    }

    /**
     * Afficher le formulaire d'édition d'un département
     */
    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    /**
     * Mettre à jour un département
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments')->ignore($department->id),
            ],
            'description' => 'nullable|string',
        ]);

        $department->update($validated);

        return redirect()->route('departments.index')
            ->with('success', 'Département mis à jour avec succès');
    }

    /**
     * Récupère les spécialités d'un département (pour AJAX)
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSpecialties(Department $department)
    {
        // Log pour débogage
        Log::info('Récupération des spécialités pour le département', [
            'department_id' => $department->id,
            'department_name' => $department->name,
            'specialties_count' => $department->specialties->count()
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $department->specialties
        ]);
    }

    /**
     * Supprimer un département
     */
    public function destroy(Department $department)
    {
        // Vérifier si le département contient des spécialités
        if ($department->specialties()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer ce département car il contient des spécialités.');
        }

        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', 'Département supprimé avec succès');
    }
}
