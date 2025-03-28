<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Vehicle;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;


class ScheduleController extends Controller {

    public function index() {

        return response()->json(Schedule::with(['category', 'vehicle'])->get());
    }



    public function store(Request $request) {

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date',
            'expiration_date' => 'required|date|after:start_date',
        ]);

        $schedule = Schedule::create($request->all());

        // Send notification if expiration is within 5 days
        if ($schedule->shouldNotify()) {

            $this->sendNotification($schedule);
        }

        return response()->json($schedule, 201);
    }



    // public function sendNotification(Schedule $schedule) {
    //     $message = "{$schedule->category->name} expires in " . Carbon::now()->diffInDays($schedule->expiration_date) . " days.";
        
    //     // Here, you would integrate with a real notification system
    //     return response()->json(['notification' => $message]);
    // }
}
