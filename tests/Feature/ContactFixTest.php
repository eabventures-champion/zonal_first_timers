<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\FirstTimer;
use App\Models\Church;
use App\Models\ChurchCategory;
use App\Models\ChurchGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\FirstTimerService;
use Tests\TestCase;

class ContactFixTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    public function test_check_contact_detects_existing_users()
    {
        $user = User::factory()->create([
            'phone' => '0243036092',
            'name' => 'Fritz Ackumey'
        ]);
        $user->assignRole('Retaining Officer');

        $response = $this->actingAs($user)->postJson(route('admin.first-timers.check-contact'), [
            'contact' => '0243036092'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'exists' => true,
        ]);
        $this->assertStringContainsString('Fritz Ackumey', $response->json('message'));
        $this->assertStringContainsString('Retaining Officer', $response->json('message'));
    }

    public function test_first_timer_service_prevents_registering_admins_as_first_timers()
    {
        $user = User::factory()->create([
            'phone' => '0243036092',
            'name' => 'Fritz Ackumey'
        ]);
        $user->assignRole('Retaining Officer');

        $category = ChurchCategory::create(['name' => 'OTHER CHURCHES']);
        $group = ChurchGroup::create(['name' => 'Test Group', 'church_category_id' => $category->id]);
        $church = Church::create([
            'name' => 'Test Church',
            'church_group_id' => $group->id,
            'address' => 'Test Address'
        ]);

        $service = app(FirstTimerService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('This phone number belongs to an existing Retaining Officer');

        $service->create([
            'full_name' => 'Testing 1',
            'primary_contact' => '0243036092',
            'church_id' => $church->id,
            'gender' => 'Male',
            'residential_address' => 'Test Address',
            'date_of_visit' => now()->toDateString()
        ]);
    }

    /**
     * This test is intended to be run against the actual database to perform cleanup if needed,
     * but here it serves as a logic verification.
     */
    public function test_cleanup_logic_removes_duplicate_roles()
    {
        $user = User::factory()->create([
            'phone' => '0243036092',
            'name' => 'Fritz Ackumey'
        ]);
        $user->assignRole('Retaining Officer');
        $user->assignRole('Member'); // The bug added this

        $this->assertTrue($user->hasRole('Retaining Officer'));
        $this->assertTrue($user->hasRole('Member'));

        // Cleanup Logic
        if ($user->hasRole('Retaining Officer') && $user->hasRole('Member')) {
            $user->removeRole('Member');
        }

        $user->refresh();
        $this->assertTrue($user->hasRole('Retaining Officer'));
        $this->assertFalse($user->hasRole('Member'));
    }
}
