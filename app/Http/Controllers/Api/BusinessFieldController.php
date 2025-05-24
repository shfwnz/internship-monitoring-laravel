<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// Models & Resources
use App\Models\BusinessField;
use App\Http\Resources\BusinessFieldResource;

class BusinessFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fields = BusinessField::all();

        return response()->json([
            'success' => true,
            'message' => 'List of business fields',
            'data' => BusinessFieldResource::collection($fields),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:business_fields,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $field = BusinessField::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Business field created',
            'data' => new BusinessFieldResource($field),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $field = BusinessField::find($id);

        if (!$field) {
            return response()->json([
                'success' => false,
                'message' => 'Business field not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Business field found',
            'data' => new BusinessFieldResource($field),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $field = BusinessField::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:business_fields,name,' . $field->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $field->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Business field updated',
            'data' => new BusinessFieldResource($field),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $field = BusinessField::find($id);

        if (!$field) {
            return response()->json([
                'success' => false,
                'message' => 'Business field not found'
            ], 404);
        }

        $field->delete();

        return response()->json([
            'success' => true,
            'message' => 'Business field deleted',
            'data' => new BusinessFieldResource($field),
        ], 200);
    }
}
