<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HealthCenter;
use App\Models\Department;
use Illuminate\Http\JsonResponse;

class HealthCenterController extends Controller
{
    /**
     * Liste de tous les centres de santé (utilisateurs avec le rôle 'reception')
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $healthCenters = HealthCenter::select(['id', 'first_name', 'last_name', 'email', 'phone', 'latitude', 'longitude'])
            ->withCount('departments')
            ->get();

        return response()->json([
            'health_centers' => $healthCenters
        ]);
    }

    /**
     * Afficher les détails d'un centre de santé avec ses départements
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        // Sanitize incoming id: strip surrounding braces, quotes and extra spaces
        $id = trim($id);
        $id = trim($id, '{}"' . "' ");

        $healthCenter = HealthCenter::where('id', $id)
            ->with(['departments' => function($query) {
                $query->select(['id', 'name', 'description', 'reception_id'])
                      ->withCount('specialties');
            }])
            ->firstOrFail();

        return response()->json([
            'health_center' => $healthCenter
        ]);
    }
}
