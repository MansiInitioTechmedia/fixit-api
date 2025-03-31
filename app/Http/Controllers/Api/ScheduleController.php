<?php

namespace App\Http\Controllers\Api;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Exception;

class ScheduleController extends Controller
{
    // Get all schedules
    public function index()
    {
        return response()->json(Schedule::with(['category', 'vehicle'])->get());
    }


    // Store a new schedule
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,id',
            'category_id' => 'required|exists:categories,id',
            'start_date' => 'required|date_format:Y-m-d',
            'expiration_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'kilometers' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1,2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => collect($validator->errors()->all())->implode(' '),
                'data' => null
            ], 422);
        }

        try {
            $validatedData = $request->all();

            $schedule = Schedule::create($validatedData);

            return response()->json([
                'status' => true,
                'message' => 'Schedule created successfully',
                'data' => $schedule
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }


    // Show a specific schedule
    public function show($id)
    {
        try {
            $schedule = Schedule::with(['vehicle', 'category'])->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Schedule retrieved successfully',
                'data' => $schedule
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Schedule not found, Please provide a valid ID',
                'data' => null
            ], 404);
        }
    }
}