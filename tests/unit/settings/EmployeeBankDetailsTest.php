<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;

class EmployeeBankDetailsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_save_bank_details()
    {
        $admin = User::where('role', User::USER_ROLE_ADMIN)->first();
        if (!$admin) {
            $admin = User::create([
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@test.com',
                'password' => bcrypt('secret'),
                'role' => User::USER_ROLE_ADMIN,
                'gender' => 'm',
                'birth_date' => '1990-01-01'
            ]);
        }

        $this->actingAs($admin);

        // Submit form data to create a new employee with bank details
        $employeeData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'gender' => 'm',
            'birth_date' => '1995-05-15',
            'bank_name' => 'Zenith Bank',
            'account_number' => '1029384756',
            'password' => 'secret123'
        ];

        // Call store route
        $response = $this->post(route('pim.employees.store'), $employeeData);
        
        // Assert employee exists in DB with correct details
        $this->assertDatabaseHas('users', [
            'email' => 'johndoe@example.com',
            'bank_name' => 'Zenith Bank',
            'account_number' => '1029384756',
            'role' => User::USER_ROLE_EMPLOYEE
        ]);
    }

    public function test_employee_can_update_own_bank_details()
    {
        $employee = User::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'janesmith@example.com',
            'password' => bcrypt('secret'),
            'role' => User::USER_ROLE_EMPLOYEE,
            'gender' => 'f',
            'birth_date' => '1992-08-20'
        ]);

        $this->actingAs($employee);

        // Submit AJAX post request to update bank details
        $response = $this->json('POST', route('employee.profile.bank_details'), [
            'bank_name' => 'GTBank',
            'account_number' => '0123456789'
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        // Assert JSON success response
        $response->assertJson(['success' => true]);

        // Assert updated in DB
        $this->assertDatabaseHas('users', [
            'id' => $employee->id,
            'bank_name' => 'GTBank',
            'account_number' => '0123456789'
        ]);
    }
}
