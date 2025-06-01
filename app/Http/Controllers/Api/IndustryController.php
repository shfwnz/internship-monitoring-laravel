<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

// Resources
use App\Http\Resources\IndustryResource;
use App\Models\Industry;

class IndustryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $industries = Industry::with('business_field')->get();

            return response()->json([
                'success' => true,
                'message' => 'success',
                'all_data' => IndustryResource::collection($industries),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Industry index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'business_field_id' => 'required|integer|exists:business_fields,id',
            'address' => 'required|string',
            'phone' => 'required|unique:industries,phone|numeric',
            'email' => 'required|email|unique:industries,email',
            'website' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data has not been created',
                'errors' => $validator->errors()
            ], 422);
        }

        $industry = Industry::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data has been created',
            'created_data' => new IndustryResource($industry),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $industry = Industry::with('business_field')->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Data has been found',
            'find_data' => new IndustryResource($industry),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $industry = Industry::with('business_field')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'business_field_id' => 'required|integer|exists:business_fields,id',
            'address' => 'required|string',
            'phone' => 'required|unique:industries,phone,' . $industry->id,
            'email' => 'required|email|unique:industries,email,' . $industry->id,
            'website' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data has not been updated',
                'errors' => $validator->errors()
            ], 422);
        }

        $industry->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data has been updated',
            'updated_data' => new IndustryResource($industry),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $industry = Industry::find($id);

        $industry->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data has been deleted',
            'deleted_data' => new IndustryResource($industry),
        ], 200);
    }
}
