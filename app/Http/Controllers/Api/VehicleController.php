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
        $vehicles = Vehicle::all();
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
            'vehicle_type' => 'nullable|string|in:car,motorcycle,truck,bicycle,bus,van,suv', // Specify valid vehicle types
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
            // Prepare vehicle data
            $vehicleData = $request->all();
            $vehicleData['vehicle_type'] = $vehicleData['vehicle_type'] ?? 'car'; // Default to 'car'
            $vehicleData['status'] = $vehicleData['status'] ?? '1'; // Default to 'available'

            // Create the vehicle
            $vehicle = Vehicle::create($vehicleData);

            // Return success response
            return response()->json([
                'status' => true,
                'message' => 'Vehicle Added successfully!',
                'data' => $vehicle
            ], 201); // Use 201 for resource creation

        } catch (\Exception $e) {
            // Return general error response
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
            $vehicle = Vehicle::findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Vehicle retrieved successfully',
                'data' => $vehicle,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Vehicle not found, Please give proper ID',
                'data' => null,
            ], 404);
        }

    }


    // Update a vehicle
    public function update(Request $request, $id)
    {
        try {
            // Find the vehicle by ID or throw a ModelNotFoundException
            $vehicle = Vehicle::findOrFail($id);

            // Validate the incoming request data
            $request->validate([
                'name' => 'sometimes|nullable|string|max:255',
                'registration_number' => 'sometimes|nullable|string|unique:vehicles,registration_number,' . $id,
                'vehicle_type' => 'sometimes|nullable|string|in:car,motorcycle,truck,bicycle,bus,van,suv', // Specify valid vehicle types
                'status' => 'sometimes|nullable|in:1,0'
            ]);

            // Prepare the data for update
            $dataToUpdate = $request->only(['name', 'registration_number', 'vehicle_type', 'status']);

            // Update the vehicle with only the provided fields
            $vehicle->update(array_filter($dataToUpdate));

            return response()->json([
                'status' => true,
                'message' => 'Vehicle updated successfully',
                'data' => $vehicle
            ], 200);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Vehicle not found, Please give proper ID',
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
            $vehicle = Vehicle::findOrFail($id);
            $vehicle->delete();

            return response()->json([
                'status' => true,
                'message' => 'Vehicle deleted successfully',
                'data' => null,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Vehicle not found, Please give proper ID',
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
