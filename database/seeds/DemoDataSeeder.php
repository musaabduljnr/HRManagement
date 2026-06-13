<?php

use Illuminate\Database\Seeder;
use App\HrPolicy;
use App\Modules\Settings\Models\Department;
use App\Modules\Settings\Models\JobTitle;
use App\Modules\Recruitment\Models\JobOpening;
use App\Modules\Recruitment\Models\CandidateApplication;
use App\User;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Departments
        $deptEng = Department::firstOrCreate(['name' => 'Engineering'], ['description' => 'Software engineering and product development.']);
        $deptHr = Department::firstOrCreate(['name' => 'Human Resources'], ['description' => 'HR, recruitment, and culture.']);
        $deptSales = Department::firstOrCreate(['name' => 'Sales'], ['description' => 'Sales and business development.']);

        // 2. Create Job Titles
        $jtDev = JobTitle::firstOrCreate(['name' => 'Senior Software Engineer'], ['description' => 'Responsible for product core features.']);
        $jtHr = JobTitle::firstOrCreate(['name' => 'HR Manager'], ['description' => 'Responsible for company hiring and employee wellness.']);
        $jtSales = JobTitle::firstOrCreate(['name' => 'Sales Associate'], ['description' => 'Responsible for client relations.']);

        // 3. Create HR Policies
        HrPolicy::firstOrCreate([
            'title' => 'Leave and Vacation Policy'
        ], [
            'content' => 'All full-time employees are entitled to 20 business days of paid annual leave. Leave requests must be submitted through the Leave management portal and require approval from your direct Department Manager at least two weeks before the scheduled departure date.',
            'category' => 'Time Off'
        ]);

        HrPolicy::firstOrCreate([
            'title' => 'Remote Work Policy'
        ], [
            'content' => 'Employees may request up to 2 days of remote work (Work From Home) per week. Coordination with your team lead is required to ensure alignment on team days. Proper high-speed internet and professional remote workspace are expected.',
            'category' => 'Work Environment'
        ]);

        HrPolicy::firstOrCreate([
            'title' => 'Code of Conduct'
        ], [
            'content' => 'We maintain a strict zero-tolerance policy for harassment, discrimination, or hostile behavior. Professionalism, respect, and mutual inclusivity are expected in all communication channels. Violations will result in disciplinary action.',
            'category' => 'Behavioral'
        ]);

        // 4. Create Job Openings
        $jo1 = JobOpening::firstOrCreate([
            'title' => 'Senior Laravel Backend Developer'
        ], [
            'description' => 'We are seeking an expert Laravel engineer to design and build modular APIs.',
            'department_id' => $deptEng->id,
            'status' => 'Open'
        ]);

        $jo2 = JobOpening::firstOrCreate([
            'title' => 'Recruiting Coordinator'
        ], [
            'description' => 'Manage candidate pipelines, set up interviews, and coordinate onboarding.',
            'department_id' => $deptHr->id,
            'status' => 'Open'
        ]);

        // 5. Create Candidate users and Candidate applications
        $candUser1 = User::firstOrCreate([
            'email' => 'jane.doe@example.com'
        ], [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'f',
            'birth_date' => '1995-04-12',
            'password' => bcrypt('password123'),
            'role' => User::USER_ROLE_CANDIDATE
        ]);

        CandidateApplication::firstOrCreate([
            'user_id' => $candUser1->id
        ], [
            'job_opening_id' => $jo1->id,
            'status' => 'Shortlisted',
            'notes' => 'Strong experience with Laravel 5 and Laravel 8. Great portfolio.'
        ]);

        $candUser2 = User::firstOrCreate([
            'email' => 'john.smith@example.com'
        ], [
            'first_name' => 'John',
            'last_name' => 'Smith',
            'gender' => 'm',
            'birth_date' => '1993-08-20',
            'password' => bcrypt('password123'),
            'role' => User::USER_ROLE_CANDIDATE
        ]);

        CandidateApplication::firstOrCreate([
            'user_id' => $candUser2->id
        ], [
            'job_opening_id' => $jo2->id,
            'status' => 'Applied',
            'notes' => 'Good communication skills, experience in tech recruiting.'
        ]);

        // 6. Create default Activity Logs
        $adminUser = User::where('role', User::USER_ROLE_ADMIN)->first();
        $adminId = $adminUser ? $adminUser->id : null;

        \DB::table('activity_logs')->insert([
            [
                'user_id' => $adminId,
                'activity' => 'Generated Payroll',
                'description' => 'Generated payroll records for the month of ' . date('Y-m'),
                'ip_address' => '127.0.0.1',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
            [
                'user_id' => $adminId,
                'activity' => 'Created Job Opening',
                'description' => 'Created Job Opening: Senior Laravel Backend Developer',
                'ip_address' => '127.0.0.1',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
            [
                'user_id' => $adminId,
                'activity' => 'System Settings Update',
                'description' => 'Configured company department structures.',
                'ip_address' => '127.0.0.1',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ]
        ]);
    }
}
