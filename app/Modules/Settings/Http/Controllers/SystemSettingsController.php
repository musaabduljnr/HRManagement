<?php

namespace App\Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\ActivityLog;

class SystemSettingsController extends Controller
{
    /**
     * Show general system settings form.
     */
    public function index()
    {
        $manualClockin = DB::table('system_settings')->where('key', 'manual_clockin_enabled')->value('value');
        $qrClockin = DB::table('system_settings')->where('key', 'qr_clockin_enabled')->value('value');

        // Defaults if not defined
        $manualClockin = ($manualClockin === null) ? 'false' : $manualClockin;
        $qrClockin = ($qrClockin === null) ? 'true' : $qrClockin;

        $current = 'settings';
        return view('settings::system', compact('manualClockin', 'qrClockin', 'current'));
    }

    /**
     * Store system settings in database.
     */
    public function store(Request $request)
    {
        $manual = $request->has('manual_clockin_enabled') ? 'true' : 'false';
        $qr = $request->has('qr_clockin_enabled') ? 'true' : 'false';

        DB::table('system_settings')->updateOrInsert(
            ['key' => 'manual_clockin_enabled'],
            ['value' => $manual, 'updated_at' => date('Y-m-d H:i:s')]
        );

        DB::table('system_settings')->updateOrInsert(
            ['key' => 'qr_clockin_enabled'],
            ['value' => $qr, 'updated_at' => date('Y-m-d H:i:s')]
        );

        ActivityLog::log(
            'System Settings Updated',
            'Attendance settings changed: Manual clock-in is ' . ($manual === 'true' ? 'enabled' : 'disabled') . ', QR clock-in is ' . ($qr === 'true' ? 'enabled' : 'disabled') . '.'
        );

        $request->session()->flash('success', 'System settings updated successfully.');
        return redirect()->route('settings.system.index');
    }
}
