<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $user = Auth::user();
        $industries = $user->hasPermissionTo('view_any_industry')
            ? Industry::with('business_field')->get()
            : Industry::with('business_field')
                ->where('user_id', $user->id)
                ->get();

        return response()->json(
            [
                'success' => true,
                'message' => 'success',
                'all_data' => IndustryResource::collection($industries),
            ],
            200,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionTo('create_industry')) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'You are not authorized to create an industry',
                ],
                403,
            );
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'business_field_id' => 'required|integer|exists:business_fields,id',
            'address' => 'required|string',
            'phone' => 'required|unique:industries,phone|numeric',
            'email' => 'required|email|unique:industries,email',
            'website' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Data has not been created',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        DB::beginTransaction();

        try {
            $industry = Industry::create($validator->validated());

            DB::commit();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Data has been created',
                    'created_data' => new IndustryResource($industry),
                ],
                201,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $industry = Industry::with('business_field')->findOrFail($id);

        return response()->json(
            [
                'success' => true,
                'message' => 'Data has been found',
                'find_data' => new IndustryResource($industry),
            ],
            200,
        );
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
            'phone' => "required|unique:industries,phone,{$industry->id}",
            'email' => "required|email|unique:industries,email,{$industry->id}",
            'website' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Data has not been updated',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        DB::beginTransaction();

        try {
            $industry->update($validator->validated());

            DB::commit();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Data has been updated',
                    'updated_data' => new IndustryResource($industry),
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $industry = Industry::find($id);

        $industry->delete();
        return response()->json(
            [
                'success' => true,
                'message' => 'Data has been deleted',
                'deleted_data' => new IndustryResource($industry),
            ],
            200,
        );
    }
}
