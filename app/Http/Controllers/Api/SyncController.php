<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SyncController extends Controller
{
    public function pull(Request $request)
    {
        // For simplicity, we are returning all needed data for offline.
        // In a real scenario, this would be based on last_synced_at.
        $employees = \App\User::select('id', 'name', 'email', 'api_token')->get();
        $rules = \Illuminate\Support\Facades\DB::table('attendance_rules')->get();
        
        return response()->json([
            'employees' => $employees,
            'attendance_rules' => $rules
        ]);
    }

    public function push(Request $request)
    {
        $actionType = $request->input('action_type');
        $payload = $request->input('payload');
        
        if ($actionType === 'attendance_checkin') {
            // Process offline check-in
            // E.g., App\Modules\Attendance\Models\Attendance::create(...)
            \Illuminate\Support\Facades\Log::info('Offline check-in synced', ['payload' => $payload]);
        } elseif ($actionType === 'attendance_checkout') {
            // Process offline check-out
            \Illuminate\Support\Facades\Log::info('Offline check-out synced', ['payload' => $payload]);
        }
        
        return response()->json(['status' => 'success']);
    }
}
