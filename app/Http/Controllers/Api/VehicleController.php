<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Vehicle;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;


class VehicleController extends Controller
{

    // Get all vehicles
    public function index()
    {
        $vehicles = Vehicle::where('user_id', auth()->id())->get();

        return response()->json([
            'status' => true,
            'message' => 'Vehicles retrieved successfully',
            'data' => $vehicles
        ], 200);
    }



    // Store a new vehicle
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:50|unique:vehicles,registration_number',
            'vehicle_type' => 'nullable|string|in:car,motorcycle,truck,bicycle,bus,van,suv',
            'status' => 'nullable|in:1,0'
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => collect($validator->errors()->all())->implode(' '),
                'data' => null,
            ], 422);
        }

        try {
            // Create the vehicle with the authenticated user's ID
            $vehicle = Vehicle::create([
                'name' => $request->name,
                'registration_number' => $request->registration_number,
                'vehicle_type' => $request->vehicle_type ?? 'car',
                'status' => $request->status ?? 'available',
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Vehicle added successfully!',
                'data' => $vehicle
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }




    // Show a single vehicle
    public function show($id)
    {
        try {
            $vehicle = Vehicle::where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            return response()->json([
                'status' => true,
                'message' => 'Vehicle retrieved successfully',
                'data' => $vehicle,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Vehicle not found or you do not have permission to view it.',
                'data' => null,
            ], 404);
        }
    }



    // Update a vehicle
    public function update(Request $request, $id)
    {
        try {
            $vehicle = Vehicle::where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $request->validate([
                'name' => 'sometimes|string|max:255',
                'registration_number' => 'sometimes|string|unique:vehicles,registration_number,' . $id,
                'vehicle_type' => 'sometimes|string|in:car,motorcycle,truck,bicycle,bus,van,suv',
                'status' => 'sometimes|in:1,0'
            ]);

            $vehicle->update($request->only(['name', 'registration_number', 'vehicle_type', 'status']));

            return response()->json([
                'status' => true,
                'message' => 'Vehicle updated successfully',
                'data' => $vehicle
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Vehicle not found or you do not have permission to update it.',
                'data' => null,
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }



    // Delete a vehicle
    public function destroy($id)
    {
        try {
            $vehicle = Vehicle::where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $vehicle->delete();

            return response()->json([
                'status' => true,
                'message' => 'Vehicle deleted successfully',
                'data' => null,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Vehicle not found or you do not have permission to delete it.',
                'data' => null,
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

}
