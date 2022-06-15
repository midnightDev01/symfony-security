<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;

class OrganizationController extends Controller
{
    /**
     * Updates an organization
     * URL: api/organizations/{organizationId}
     * Method: PUT
     *
     * @param UpdateOrganizationRequest $request
     * @param Organization              $organization
     * @return JsonResponse
     */
    public function update(UpdateOrganizationRequest $request, Organization $organization): JsonResponse
    {
        $errors = 0;

        $errors += $organization->update($request->getAcceptedFields()) ? 0 : 1;

        if ($errors === 0) {
            return response()->json([
                'data' => $organization,
            ]);
        }

        return response()->json([
            'message' => 'failure',
        ], 500);
    }
}
