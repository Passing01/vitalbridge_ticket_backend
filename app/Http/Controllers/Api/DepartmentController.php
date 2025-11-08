<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    /**
     * Afficher les détails d'un département avec ses spécialités
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        // Nettoyer l'ID en enlevant les accolades si présentes
        $cleanId = trim($id, '{}');
        
        // Vérifier si l'ID est numérique
        if (!is_numeric($cleanId)) {
            return response()->json([
                'message' => 'ID de département invalide',
                'requested_id' => $id,
                'clean_id' => $cleanId,
                'available_departments' => Department::select('id', 'name')->get()
            ], 400);
        }
        
        // Récupérer le département avec ses spécialités
        $department = Department::with(['specialties' => function($query) {
            $query->select(['id', 'name', 'description', 'department_id'])
                  ->withCount('doctorProfiles as doctors_count');
        }])->find($cleanId);

        // Si le département n'existe pas
        if (!$department) {
            return response()->json([
                'message' => 'Département non trouvé',
                'requested_id' => $cleanId,
                'available_departments' => Department::select('id', 'name')->get()
            ], 404);
        }

        // Vérification des données
        $debug = [
            'department_id' => $department->id,
            'department_name' => $department->name,
            'specialties_count' => $department->specialties->count(),
            'specialties_ids' => $department->specialties->pluck('id')
        ];

        return response()->json([
            'department' => $department,
            '_debug' => $debug
        ]);
    }
}
