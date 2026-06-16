<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Attendance;
use App\AttendanceRule;
use App\EmployeeDevice;
use App\Services\AttendanceService;
use App\Modules\Settings\Models\Department;
use Carbon\Carbon;

class AttendanceServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected $service;

    protected function setUp()
    {
        parent::setUp();
        $this->service = new AttendanceService();
    }

    /**
     * Test rules precedence resolution: Employee-specific > Department > Company-wide
     */
    public function test_rule_precedence_resolution()
    {
        // 1. Create a Department
        $dept = Department::create(['name' => 'Engineering']);

        // 2. Create User
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'testuser@example.com',
            'password' => bcrypt('secret'),
            'role' => User::USER_ROLE_EMPLOYEE,
            'gender' => 'm',
            'birth_date' => '1995-01-01',
            'department_id' => $dept->id
        ]);

        // Define a Monday date
        $mondayDate = '2026-06-15'; // 2026-06-15 is Monday

        // 3. Create Company Rule
        $companyRule = AttendanceRule::create([
            'company_id' => 1,
            'rule_name' => 'Company Rule',
            'shift_name' => 'Standard Shift',
            'applies_to' => 'all_employees',
            'working_days' => json_encode(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']),
            'check_in_start_time' => '08:00:00',
            'check_in_cutoff_time' => '10:00:00',
            'status' => 'active'
        ]);

        // Resolves to Company Rule
        $resolvedRule = $this->service->getApplicableRuleForUser($user, $mondayDate);
        $this->assertNotNull($resolvedRule);
        $this->assertEquals($companyRule->id, $resolvedRule->id);

        // 4. Create Department Rule
        $deptRule = AttendanceRule::create([
            'company_id' => 1,
            'rule_name' => 'Dept Rule',
            'shift_name' => 'Engineering Shift',
            'applies_to' => 'department',
            'department_id' => $dept->id,
            'working_days' => json_encode(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']),
            'check_in_start_time' => '08:00:00',
            'check_in_cutoff_time' => '10:00:00',
            'status' => 'active'
        ]);

        // Resolves to Department Rule
        $resolvedRule = $this->service->getApplicableRuleForUser($user, $mondayDate);
        $this->assertNotNull($resolvedRule);
        $this->assertEquals($deptRule->id, $resolvedRule->id);

        // 5. Create Employee Rule
        $empRule = AttendanceRule::create([
            'company_id' => 1,
            'rule_name' => 'Emp Rule',
            'shift_name' => 'Custom Shift',
            'applies_to' => 'selected_employees',
            'employee_ids' => json_encode([$user->id]),
            'working_days' => json_encode(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']),
            'check_in_start_time' => '08:00:00',
            'check_in_cutoff_time' => '10:00:00',
            'status' => 'active'
        ]);

        // Resolves to Employee Rule
        $resolvedRule = $this->service->getApplicableRuleForUser($user, $mondayDate);
        $this->assertNotNull($resolvedRule);
        $this->assertEquals($empRule->id, $resolvedRule->id);
    }

    /**
     * Test GPS radius and validation
     */
    public function test_gps_validation_distance()
    {
        // Office in Lagos (6.5244, 3.3792)
        $latOffice = 6.5244;
        $lonOffice = 3.3792;

        $user = User::create([
            'first_name' => 'GPS',
            'last_name' => 'Test',
            'email' => 'gpstest@example.com',
            'password' => bcrypt('secret'),
            'role' => User::USER_ROLE_EMPLOYEE,
            'gender' => 'm',
            'birth_date' => '1995-01-01',
        ]);

        $rule = AttendanceRule::create([
            'company_id' => 1,
            'rule_name' => 'GPS Rule',
            'shift_name' => 'Lagos Shift',
            'applies_to' => 'all_employees',
            'working_days' => json_encode(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']),
            'check_in_start_time' => '00:00:00',
            'check_in_cutoff_time' => '23:59:59',
            'office_latitude' => $latOffice,
            'office_longitude' => $lonOffice,
            'allowed_radius_meters' => 100, // 100 meters limit
            'status' => 'active'
        ]);

        // Calculate distance helper check
        // Inside radius check (approx 45 meters away)
        $distance = $this->service->calculateDistance($latOffice + 0.0003, $lonOffice + 0.0003, $latOffice, $lonOffice);
        $this->assertLessThan(100, $distance);

        // Outside radius check (approx 9 km away)
        $distanceFar = $this->service->calculateDistance($latOffice + 0.08, $lonOffice + 0.08, $latOffice, $lonOffice);
        $this->assertGreaterThan(100, $distanceFar);

        // Check validation with valid location
        $validationValid = $this->service->validateCheckIn($user, $rule, [
            'latitude' => $latOffice + 0.0003,
            'longitude' => $lonOffice + 0.0003
        ]);
        $this->assertTrue($validationValid['status']);

        // Check validation with invalid location
        $validationInvalid = $this->service->validateCheckIn($user, $rule, [
            'latitude' => $latOffice + 0.08,
            'longitude' => $lonOffice + 0.08
        ]);
        $this->assertFalse($validationInvalid['status']);
        $this->assertContains('Location validation failed', $validationInvalid['message']);
    }

    /**
     * Test overnight shift check-in / check-out matching.
     */
    public function test_overnight_shift_matching()
    {
        $user = User::create([
            'first_name' => 'Night',
            'last_name' => 'Shift',
            'email' => 'night@example.com',
            'password' => bcrypt('secret'),
            'role' => User::USER_ROLE_EMPLOYEE,
            'gender' => 'm',
            'birth_date' => '1995-01-01',
        ]);

        $rule = AttendanceRule::create([
            'company_id' => 1,
            'rule_name' => 'Overnight Shift Rule',
            'shift_name' => 'Night Shift',
            'applies_to' => 'all_employees',
            'working_days' => json_encode(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']),
            'check_in_start_time' => '18:00:00',
            'check_in_cutoff_time' => '23:59:59',
            'check_out_enabled' => true,
            'status' => 'active'
        ]);

        $yesterday = Carbon::yesterday()->toDateString();
        $today = Carbon::today()->toDateString();

        // 1. Simulate a check-in yesterday at 19:00:00
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $yesterday,
            'check_in' => Carbon::yesterday()->setTime(19, 0, 0),
            'status' => 'Present',
            'attendance_rule_id' => $rule->id,
            'company_id' => 1
        ]);

        // 2. Perform checkout today at 04:00:00 AM (Overnight)
        // Mock the current time to today 04:00:00 for the service
        Carbon::setTestNow(Carbon::today()->setTime(4, 0, 0));

        $checkoutResult = $this->service->processCheckOut($user, []);

        $this->assertEquals('success', $checkoutResult['status']);
        $this->assertEquals($attendance->id, $checkoutResult['attendance']->id);
        
        $updatedAttendance = Attendance::find($attendance->id);
        $this->assertNotNull($updatedAttendance->check_out);
        $this->assertEquals(540, $updatedAttendance->work_duration_minutes); // 9 hours = 540 minutes

        // Clear mocked time
        Carbon::setTestNow();
    }

    public function test_admin_create_rule_empty_fields()
    {
        $data = [
            'company_id' => 1,
            'rule_name' => 'Test Rule Empty Fields',
            'shift_name' => 'Standard Shift',
            'applies_to' => 'all_employees',
            'working_days' => json_encode(['Monday', 'Tuesday']),
            'check_in_start_time' => '08:00:00',
            'check_in_cutoff_time' => '10:00:00',
            'check_out_start_time' => '', 
            'check_out_cutoff_time' => '', 
            'office_latitude' => '', 
            'office_longitude' => '', 
            'allowed_radius_meters' => '', 
            'minimum_work_duration_minutes' => '', 
            'grace_period_minutes' => '', 
            'status' => 'active'
        ];

        $rule = AttendanceRule::create($data);
        $this->assertNotNull($rule);
    }
}

