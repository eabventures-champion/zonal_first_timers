<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Member;
use App\Models\FirstTimer;
use App\Models\Bringer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BringerContactValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    protected function createChurchStructure($suffix = '')
    {
        $category = \App\Models\ChurchCategory::create(['name' => 'Test Cat ' . $suffix]);
        $group = \App\Models\ChurchGroup::create(['name' => 'Test Group ' . $suffix, 'church_category_id' => $category->id]);
        return \App\Models\Church::create([
            'name' => 'Test Church ' . $suffix,
            'church_group_id' => $group->id,
            'address' => 'Test Address'
        ]);
    }

    public function test_bringer_check_contact_detects_existing_user()
    {
        $user = User::factory()->create([
            'phone' => '0243036092',
            'name' => 'Fritz Ackumey'
        ]);
        $user->assignRole('Retaining Officer');

        $response = $this->actingAs($user)->postJson(route('admin.bringers.check-contact'), [
            'contact' => '0243036092'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'exists' => true,
        ]);
        $this->assertStringContainsString('Fritz Ackumey', $response->json('message'));
        $this->assertStringContainsString('Retaining Officer', $response->json('message'));
    }

    public function test_bringer_check_contact_detects_existing_member()
    {
        $user = User::factory()->create();
        $user->assignRole('Retaining Officer');

        $church = $this->createChurchStructure('M');

        $member = Member::create([
            'full_name' => 'Member One',
            'primary_contact' => '0505123456',
            'church_id' => $church->id,
            'gender' => 'Male',
            'marital_status' => 'Single',
            'residential_address' => 'Test Address',
            'date_of_visit' => now()->toDateString()
        ]);

        $response = $this->actingAs($user)->postJson(route('admin.bringers.check-contact'), [
            'contact' => '0505123456'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'exists' => true,
        ]);
        $this->assertStringContainsString('Member One', $response->json('message'));
        $this->assertStringContainsString('Member', $response->json('message'));
    }

    public function test_bringer_check_contact_detects_existing_first_timer()
    {
        $user = User::factory()->create();
        $user->assignRole('Retaining Officer');

        $church = $this->createChurchStructure('FT');

        $ft = FirstTimer::create([
            'full_name' => 'FT One',
            'primary_contact' => '0241112223',
            'church_id' => $church->id,
            'gender' => 'Male',
            'marital_status' => 'Single',
            'residential_address' => 'Test Address',
            'date_of_visit' => now()->toDateString()
        ]);

        $response = $this->actingAs($user)->postJson(route('admin.bringers.check-contact'), [
            'contact' => '0241112223'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'exists' => true,
        ]);
        $this->assertStringContainsString('FT One', $response->json('message'));
        $this->assertStringContainsString('First Timer', $response->json('message'));
    }

    public function test_bringer_check_contact_detects_existing_bringer()
    {
        $user = User::factory()->create();
        $user->assignRole('Retaining Officer');

        $church = $this->createChurchStructure('B');

        $bringer = Bringer::create([
            'contact' => '0200000000',
            'name' => 'Existing Bringer',
            'church_id' => $church->id
        ]);

        $response = $this->actingAs($user)->postJson(route('admin.bringers.check-contact'), [
            'contact' => '0200000000'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'exists' => true,
        ]);
        $this->assertStringContainsString('A Bringer with this contact already exists', $response->json('message'));
    }
}
