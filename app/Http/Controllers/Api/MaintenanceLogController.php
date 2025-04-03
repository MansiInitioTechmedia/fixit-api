<?php

namespace App\Http\Controllers\Api;

use App\Models\MaintenanceLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Auth;


class MaintenanceLogController extends Controller
{
    // Get all maintenance logs
    public function index(Request $request)
    {
        try {
            $user = auth()->user(); // Get the authenticated user
            $logs = MaintenanceLog::where('user_id', $user->id)->get()->map(function ($log) {
                return array_merge($log->toArray(), [
                    'receipts' => str_pad(
                        isset($log->receipts) && is_array($log->receipts) ? count($log->receipts) : 0,
                        2,
                        '0',
                        STR_PAD_LEFT
                    )
                ]);
            });

            return response()->json([
                'status' => true,
                'message' => 'Logs retrieved successfully',
                'data' => $logs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null
            ]);
        }
    }




    // Store a new maintenance log
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'car_id' => 'required|exists:vehicles,id',
            'car_name' => 'required|string|max:255',
            'service_type' => 'required|string|max:255',
            'maintenance_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'receipts' => 'sometimes|string',
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
            $validatedData['user_id'] = Auth::id(); // Assign logged-in user

            $log = MaintenanceLog::create($validatedData);

            return response()->json([
                'status' => true,
                'message' => 'Maintenance log added successfully',
                'data' => $log
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    // Remove a maintenance log
    public function destroy($id)
    {
        try {
            $log = MaintenanceLog::where('id', $id)->where('user_id', Auth::id())->first();

            if (!$log) {
                return response()->json([
                    'status' => false,
                    'message' => 'Maintenance log not found or you do not have permission to delete it.',
                    'data' => null
                ], 404);
            }

            $log->delete();

            return response()->json([
                'status' => true,
                'message' => 'Maintenance log deleted successfully',
                'data' => null
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Maintenance log not found.',
                'data' => null
            ], 404);
        }
    }
}
