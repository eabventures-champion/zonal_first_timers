<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name,
            'primary_contact' => $this->faker->phoneNumber,
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'date_of_visit' => now(),
            'date_of_birth' => $this->faker->date(),
            'age' => $this->faker->numberBetween(18, 80),
            'residential_address' => $this->faker->address,
            'occupation' => $this->faker->jobTitle,
            'marital_status' => $this->faker->randomElement(['Single', 'Married']),
            'status' => 'Retained',
            'membership_approved_at' => now(),
            'acknowledged_at' => null,
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }
}
