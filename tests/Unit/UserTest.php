<?php

namespace Tests\Unit;

use App\Models\Church;
use App\Models\ChurchCategory;
use App\Models\ChurchGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    public function test_super_admin_is_considered_other_church_ro()
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $this->assertTrue($user->isOtherChurchRO());
    }

    public function test_admin_is_considered_other_church_ro()
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $this->assertTrue($user->isOtherChurchRO());
    }

    public function test_other_church_ro_passes_check_with_trailing_spaces()
    {
        $category = ChurchCategory::create(['name' => 'OTHER CHURCHES ']); // Trailing space
        $group = ChurchGroup::create(['name' => 'Test Group', 'church_category_id' => $category->id]);
        $church = Church::create([
            'name' => 'Test Church',
            'church_group_id' => $group->id,
            'address' => 'Test Address'
        ]);

        $user = User::factory()->create(['church_id' => $church->id]);
        $user->assignRole('Retaining Officer');

        $this->assertTrue($user->isOtherChurchRO());
    }

    public function test_main_church_ro_fails_check()
    {
        $category = ChurchCategory::create(['name' => 'MAIN CHURCH']);
        $group = ChurchGroup::create(['name' => 'Test Group', 'church_category_id' => $category->id]);
        $church = Church::create([
            'name' => 'Test Church',
            'church_group_id' => $group->id,
            'address' => 'Test Address'
        ]);

        $user = User::factory()->create(['church_id' => $church->id]);
        $user->assignRole('Retaining Officer');

        $this->assertFalse($user->isOtherChurchRO());
    }

    public function test_regular_user_fails_check()
    {
        $user = User::factory()->create();
        // No role assigned

        $this->assertFalse($user->isOtherChurchRO());
    }
}
