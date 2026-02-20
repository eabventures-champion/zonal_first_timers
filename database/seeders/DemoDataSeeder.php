<?php

namespace Database\Seeders;

use App\Models\Church;
use App\Models\ChurchCategory;
use App\Models\ChurchGroup;
use App\Models\FirstTimer;
use App\Models\User;
use App\Models\WeeklyAttendance;
use App\Models\FoundationAttendance;
use App\Models\FoundationClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ──────────────────────────────────────────

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@church.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
            ]
        );
        $superAdmin->assignRole('Super Admin');

        $admin = User::firstOrCreate(
            ['email' => 'admin@church.com'],
            [
                'name' => 'Church Administrator',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('Admin');

        // ── Church Hierarchy ───────────────────────────────

        $categories = [
            [
                'name' => 'Region 1',
                'description' => 'Northern region churches',
                'groups' => [
                    [
                        'name' => 'Zone A',
                        'churches' => ['Grace Chapel', 'Mercy Assembly', 'Faith Tabernacle'],
                    ],
                    [
                        'name' => 'Zone B',
                        'churches' => ['Hope Cathedral', 'Love Center'],
                    ],
                ],
            ],
            [
                'name' => 'Region 2',
                'description' => 'Southern region churches',
                'groups' => [
                    [
                        'name' => 'Zone C',
                        'churches' => ['Victory Church', 'Triumph Center'],
                    ],
                    [
                        'name' => 'Zone D',
                        'churches' => ['Glory Tabernacle'],
                    ],
                ],
            ],
        ];

        $officerCount = 1;
        $officers = [];

        foreach ($categories as $catData) {
            $category = ChurchCategory::firstOrCreate(
                ['name' => $catData['name']],
                ['description' => $catData['description'], 'created_by' => $superAdmin->id]
            );

            foreach ($catData['groups'] as $grpData) {
                $group = ChurchGroup::firstOrCreate(
                    ['name' => $grpData['name'], 'church_category_id' => $category->id],
                    ['description' => "Churches in {$grpData['name']}", 'created_by' => $superAdmin->id]
                );

                foreach ($grpData['churches'] as $churchName) {
                    // Create retaining officer
                    $officer = User::firstOrCreate(
                        ['email' => "officer{$officerCount}@church.com"],
                        [
                            'name' => "Officer {$officerCount} ({$churchName})",
                            'password' => Hash::make('password'),
                        ]
                    );
                    $officer->assignRole('Retaining Officer');
                    $officers[$officerCount] = $officer;

                    $church = Church::firstOrCreate(
                        ['name' => $churchName, 'church_group_id' => $group->id],
                        [
                            'address' => "{$churchName} Street, City",
                            'retaining_officer_id' => $officer->id,
                            'created_by' => $superAdmin->id,
                        ]
                    );

                    // Assign officer to church
                    $officer->update(['church_id' => $church->id]);

                    // Create first timers for each church
                    $this->createFirstTimersForChurch($church, $officer, $superAdmin);

                    $officerCount++;
                }
            }
        }
    }

    private function createFirstTimersForChurch(Church $church, User $officer, User $creator): void
    {
        $firstNames = ['John', 'Mary', 'David', 'Sarah', 'Peter', 'Ruth', 'James'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Davis', 'Wilson'];
        $genders = ['Male', 'Female'];
        $statuses = ['Single', 'Married', 'Divorced', 'Widowed'];
        $ftStatuses = ['New', 'Developing', 'Retained'];

        for ($i = 0; $i < rand(5, 8); $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $uniqueSuffix = $church->id . '_' . $i . '_' . rand(100, 999);

            $ft = FirstTimer::create([
                'church_id' => $church->id,
                'full_name' => "{$firstName} {$lastName}",
                'primary_contact' => '080' . rand(10000000, 99999999),
                'alternate_contact' => rand(0, 1) ? '081' . rand(10000000, 99999999) : null,
                'gender' => $genders[array_rand($genders)],
                'date_of_birth' => now()->subYears(rand(18, 60))->subDays(rand(0, 365)),
                'age' => rand(18, 60),
                'residential_address' => rand(1, 100) . " Sample Street, City",
                'occupation' => ['Student', 'Engineer', 'Teacher', 'Doctor', 'Business Owner', 'Civil Servant'][array_rand(['Student', 'Engineer', 'Teacher', 'Doctor', 'Business Owner', 'Civil Servant'])],
                'marital_status' => $statuses[array_rand($statuses)],
                'email' => strtolower("{$firstName}.{$lastName}.{$uniqueSuffix}@example.com"),
                'bringer_name' => rand(0, 1) ? 'Bringer ' . rand(1, 50) : null,
                'bringer_contact' => rand(0, 1) ? '090' . rand(10000000, 99999999) : null,
                'bringer_fellowship' => rand(0, 1) ? 'Fellowship ' . rand(1, 10) : null,
                'born_again' => rand(0, 1),
                'water_baptism' => rand(0, 1),
                'prayer_requests' => rand(0, 1) ? 'Prayer request for guidance and wisdom' : null,
                'date_of_visit' => now()->subDays(rand(1, 90)),
                'church_event' => ['Sunday Service', 'Midweek Service', 'Special Program', 'Outreach'][array_rand(['Sunday Service', 'Midweek Service', 'Special Program', 'Outreach'])],
                'status' => $ftStatuses[array_rand($ftStatuses)],
                'retaining_officer_id' => $officer->id,
                'created_by' => $creator->id,
            ]);

            // Add some weekly attendance
            for ($w = 1; $w <= rand(1, 6); $w++) {
                WeeklyAttendance::create([
                    'first_timer_id' => $ft->id,
                    'church_id' => $church->id,
                    'week_number' => $w,
                    'service_date' => now()->subWeeks(6 - $w),
                    'attended' => rand(0, 1),
                    'recorded_by' => $officer->id,
                ]);
            }

            // Add some foundation school progress
            $classes = FoundationClass::ordered()->get();
            $classLimit = rand(0, $classes->count());
            foreach ($classes->take($classLimit) as $class) {
                FoundationAttendance::create([
                    'first_timer_id' => $ft->id,
                    'foundation_class_id' => $class->id,
                    'attended' => true,
                    'completed' => rand(0, 1),
                    'attendance_date' => now()->subDays(rand(1, 60)),
                ]);
            }
        }
    }
}
