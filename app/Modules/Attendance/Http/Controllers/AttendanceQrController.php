<?php

namespace App\Modules\Attendance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\AttendanceQrToken;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceQrController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Generate a new rotating QR code token for the authenticated employee.
     */
    public function generateToken(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $rule = $this->attendanceService->getApplicableRuleForUser($user);

        if (!$rule) {
            return response()->json([
                'error' => 'No active attendance rule resolved for your profile today. Please contact HR.'
            ], 403);
        }

        // Generate a 40-character unique token
        $tokenStr = str_random(40);
        $expiresAt = Carbon::now()->addSeconds(35); // 35 seconds to account for rotation lag

        $token = AttendanceQrToken::create([
            'token' => $tokenStr,
            'user_id' => $user->id,
            'attendance_rule_id' => $rule->id,
            'expires_at' => $expiresAt
        ]);

        return response()->json([
            'token' => $token->token,
            'expires_in' => 30, // Tell client to rotate in 30 seconds
            'rule_name' => $rule->rule_name,
            'shift_name' => $rule->shift_name
        ]);
    }
}
