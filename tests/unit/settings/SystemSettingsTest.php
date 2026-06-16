<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use Illuminate\Support\Facades\DB;

class SystemSettingsTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $employee;

    protected function setUp()
    {
        parent::setUp();

        // Find or create admin
        $this->admin = User::where('role', User::USER_ROLE_ADMIN)->first();
        if (!$this->admin) {
            $this->admin = User::create([
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@systemsettings.com',
                'password' => bcrypt('secret'),
                'role' => User::USER_ROLE_ADMIN,
                'gender' => 'm',
                'birth_date' => '1990-01-01'
            ]);
        }

        // Find or create employee
        $this->employee = User::where('role', User::USER_ROLE_EMPLOYEE)->first();
        if (!$this->employee) {
            $this->employee = User::create([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@systemsettings.com',
                'password' => bcrypt('secret'),
                'role' => User::USER_ROLE_EMPLOYEE,
                'gender' => 'm',
                'birth_date' => '1995-01-01'
            ]);
        }
    }

    /**
     * Test admin system settings view.
     */
    public function test_admin_can_access_system_settings()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('settings.system.index'));
        $response->assertStatus(200);
        $response->assertSee('System Settings');
        $response->assertSee('Attendance Configurations');
    }

    /**
     * Test admin can update system settings.
     */
    public function test_admin_can_save_settings()
    {
        $this->actingAs($this->admin);

        // Turn both on
        $response = $this->post(route('settings.system.store'), [
            'manual_clockin_enabled' => '1',
            'qr_clockin_enabled' => '1',
        ]);
        $response->assertRedirect(route('settings.system.index'));

        $this->assertEquals('true', DB::table('system_settings')->where('key', 'manual_clockin_enabled')->value('value'));
        $this->assertEquals('true', DB::table('system_settings')->where('key', 'qr_clockin_enabled')->value('value'));

        // Turn both off
        $response = $this->post(route('settings.system.store'), []);
        $response->assertRedirect(route('settings.system.index'));

        $this->assertEquals('false', DB::table('system_settings')->where('key', 'manual_clockin_enabled')->value('value'));
        $this->assertEquals('false', DB::table('system_settings')->where('key', 'qr_clockin_enabled')->value('value'));
    }

    /**
     * Test manual clock-in restriction when disabled.
     */
    public function test_manual_clockin_blocked_when_disabled()
    {
        // Set manual clock-in to disabled
        DB::table('system_settings')->updateOrInsert(
            ['key' => 'manual_clockin_enabled'],
            ['value' => 'false', 'updated_at' => date('Y-m-d H:i:s')]
        );

        $this->actingAs($this->employee);

        // Attempt manual clock-in post
        $response = $this->post(route('employee.attendance.web_clock'), []);
        
        // Should redirect back with an error session message
        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Manual web clock-in is disabled by administrator.');
    }

    /**
     * Test QR clock-in restriction when disabled.
     */
    public function test_qr_clockin_blocked_when_disabled()
    {
        // Set QR clock-in to disabled
        DB::table('system_settings')->updateOrInsert(
            ['key' => 'qr_clockin_enabled'],
            ['value' => 'false', 'updated_at' => date('Y-m-d H:i:s')]
        );

        $this->actingAs($this->employee);

        // Attempt accessing QR page
        $response = $this->get(route('employee.attendance.qr'));
        
        // Should redirect to employee home with error
        $response->assertRedirect(route('employee.home'));
        $response->assertSessionHas('error', 'QR Code clock-in is disabled by administrator.');
    }
}
