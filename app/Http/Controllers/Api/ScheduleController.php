<?php

namespace App\Http\Controllers\Api;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    // Get all schedules
    public function index()
    {
        $userId = Auth::id();
        $schedules = Schedule::where('user_id', $userId)->with(['category', 'vehicle'])->get();

        return response()->json([
            'status' => true,
            'message' => 'Schedules retrieved successfully',
            'data' => $schedules
        ], 200);
    }



    // Store a new schedule
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,id',
            'category_id' => 'required|exists:categories,id',
            'start_date' => 'nullable|date_format:Y-m-d',
            'expiration_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'kilometers' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1,2,3',
            'service_date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => collect($validator->errors()->all())->implode(' '),
                'data' => null
            ], 422);
        }

        try {
            $schedule = Schedule::create([
                'user_id' => Auth::id(),
                'vehicle_id' => $request->vehicle_id,
                'category_id' => $request->category_id,
                'start_date' => $request->start_date,
                'expiration_date' => $request->expiration_date,
                'kilometers' => $request->kilometers,
                'status' => $request->status,
                'service_date' => $request->service_date,
            ]);

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
            $schedule = Schedule::where('user_id', Auth::id())->with(['vehicle', 'category'])->findOrFail($id);

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
    public function getSchedulesByStatus(Request $request)
    {
        $validStatuses = [0, 1, 2, 3];

        $request->validate([
            'service_status' => 'required|integer|in:' . implode(',', $validStatuses),
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1',
        ]);

        $serviceStatus = $request->input('service_status');

        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);

        try {
            $schedules = Schedule::where('user_id', Auth::id())
                ->where('status', $serviceStatus)
                ->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'status' => true,
                'message' => 'Record fetch successfully.',
                'data' => [
                    'total' => $schedules->total(),
                    'perPage' => $schedules->perPage(),
                    'currentPage' => $schedules->currentPage(),
                    'lastPage' => $schedules->lastPage(),
                    'servicesSchedule' => $schedules->items(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'error' => null,
            ], 500);
        }
    }
}