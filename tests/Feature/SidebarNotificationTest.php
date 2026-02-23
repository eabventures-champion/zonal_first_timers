<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use App\Models\Church;
use App\Models\ChurchCategory;
use App\Models\ChurchGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class SidebarNotificationTest extends TestCase
{
    use RefreshDatabase;
    protected $church;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup basic requirements
        Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Retaining Officer']);
        Role::create(['name' => 'Member']);

        $category = ChurchCategory::create(['name' => 'Test Category']);
        $group = ChurchGroup::create(['name' => 'Test Group', 'church_category_id' => $category->id]);
        $this->church = Church::create([
            'name' => 'Test Church',
            'church_group_id' => $group->id,
        ]);
    }

    public function test_sidebar_shows_pending_approvals_count_for_admin()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        // Create 7 members with NULL acknowledged_at
        Member::factory()->count(7)->create([
            'church_id' => $this->church->id,
            'acknowledged_at' => null
        ]);

        // Create one specifically with a foundation class to test the display
        $m = Member::factory()->create([
            'church_id' => $this->church->id,
            'acknowledged_at' => null,
            'full_name' => 'Test Member Level'
        ]);

        $fClass = \App\Models\FoundationClass::create([
            'name' => 'The New Creature',
            'class_number' => 1
        ]);

        // Create a second class so the member is "in-progress" (not all classes completed)
        \App\Models\FoundationClass::create([
            'name' => 'The New Creation',
            'class_number' => 2
        ]);

        \App\Models\FoundationAttendance::create([
            'member_id' => $m->id,
            'foundation_class_id' => $fClass->id,
            'completed' => true,
            'service_date' => now()
        ]);

        $response = $this->actingAs($admin)->get(route('admin.membership-approvals.index'));

        $response->assertStatus(200);
        $response->assertSee('Class: 1 The New Creature');
        $response->assertSee('in-progress');

        // Count is 7 + 1 = 8
        $response->assertSee('8');
    }

    public function test_sidebar_hides_badge_when_no_pending_approvals()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        // Create 1 member with non-NULL acknowledged_at
        Member::factory()->create([
            'church_id' => $this->church->id,
            'acknowledged_at' => now()
        ]);

        $response = $this->actingAs($admin)->get(route('admin.membership-approvals.index'));

        $response->assertStatus(200);
        $response->assertDontSee('bg-orange-500');
    }

    public function test_sidebar_scopes_count_for_retaining_officer()
    {
        $ro = User::factory()->create(['church_id' => $this->church->id]);
        $ro->assignRole('Retaining Officer');

        $church2 = Church::create([
            'name' => 'Another Church',
            'church_group_id' => $this->church->church_group_id,
        ]);

        // 133 members in RO's church
        Member::factory()->count(133)->create([
            'church_id' => $this->church->id,
            'acknowledged_at' => null
        ]);

        // 55 members in another church
        Member::factory()->count(55)->create([
            'church_id' => $church2->id,
            'acknowledged_at' => null
        ]);

        $response = $this->actingAs($ro)->get(route('ro.members.index'));

        $response->assertStatus(200);
        $response->assertSee('133');
        $response->assertDontSee('188'); // Total would be 188, but it should only see 133
    }
}
