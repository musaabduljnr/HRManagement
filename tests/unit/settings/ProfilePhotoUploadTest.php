<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfilePhotoUploadTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;

    protected function setUp()
    {
        parent::setUp();
        
        $this->admin = User::where('role', User::USER_ROLE_ADMIN)->first();
        if (!$this->admin) {
            $this->admin = User::create([
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
                'role' => User::USER_ROLE_ADMIN,
                'gender' => 'm',
                'birth_date' => '1990-01-01'
            ]);
        }
    }

    public function test_admin_can_upload_profile_photo()
    {
        Storage::fake('public');

        $this->actingAs($this->admin);

        // Upload fake file
        $file = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->post(route('profile.photo.upload'), [
            'profile_photo' => $file
        ]);
        if ($response->status() !== 200) {
            dd($response->getContent());
        }
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Refresh user from DB
        $user = $this->admin->fresh();
        $this->assertNotEmpty($user->profile_photo);

        // Assert file exists in public storage
        Storage::disk('public')->assertExists($user->profile_photo);

        // Access profile page and verify it sees the photo
        $profileResponse = $this->get(route('profile.index'));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee($user->profile_photo_url);
    }
}
