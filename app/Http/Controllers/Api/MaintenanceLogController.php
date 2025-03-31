<?php

namespace App\Http\Controllers\Api;

use App\Models\MaintenanceLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Exception;

class MaintenanceLogController extends Controller
{
    // Get all maintenance logs
    public function index()
    {
        $logs = MaintenanceLog::all()->map(function ($log) {
            return array_merge($log->toArray(), [
                'receipts' => str_pad(isset($log->receipts) ? count(explode(',', $log->receipts)) : 0, 2, '0', STR_PAD_LEFT)
            ]);
        });

        return response()->json($logs);
    }

    // Store a new maintenance log
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'car_name'         => 'required|string|max:255',
            'service_type'     => 'required|string|max:255',
            'maintenance_date' => 'required|date',
            'amount'           => 'required|numeric|min:0',
            'receipts'         => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => collect($validator->errors()->all())->implode(' '),
                'data'    => null
            ], 422);
        }

        try {
            $validatedData = $request->all();

             // Count number of images in receipts if provided
             $receiptsCount = isset($validatedData['receipts']) ? count(explode(',', $validatedData['receipts'])) : 0;

            $log = MaintenanceLog::create($validatedData);
            return response()->json([
                'status'  => true,
                'message' => 'Maintenance log added successfully',
                'data'    => array_merge($log->toArray(), [
                    'receipts' => str_pad($receiptsCount, 2, '0', STR_PAD_LEFT)
                ])
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
                'data'    => null
            ], 500);
        }
    }


    // Remove a maintenance log
    public function destroy($id)
    {
        try {
            $log = MaintenanceLog::findOrFail($id);
            $log->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Maintenance log deleted successfully',
                'data'    => null
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Maintenance log not found, Please provide a valid ID',
                'data'    => null
            ], 404);
        }
    }
}
