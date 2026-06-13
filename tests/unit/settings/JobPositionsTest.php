<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;

class JobPositionsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function test_job_posting_list()
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

        $response = $this->get(route('settings.job_positions.index'));
        $response->assertStatus(200);
        $response->assertSee('Job Positions');
    }
}
