<?php

namespace Database\Seeders;

use App\Models\FoundationClass;
use Illuminate\Database\Seeder;

class FoundationClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            ['name' => 'Class 1 - New Life', 'class_number' => 1, 'description' => 'Introduction to the Christian faith and new life in Christ'],
            ['name' => 'Class 2 - Water Baptism', 'class_number' => 2, 'description' => 'Understanding water baptism by immersion'],
            ['name' => 'Class 3 - Holy Spirit', 'class_number' => 3, 'description' => 'The person and work of the Holy Spirit'],
            ['name' => 'Class 4 - Prayer', 'class_number' => 4, 'description' => 'Developing a strong prayer life'],
            ['name' => 'Class 5 - Bible Study', 'class_number' => 5, 'description' => 'How to study and apply the Word of God'],
            ['name' => 'Class 6 - Fellowship', 'class_number' => 6, 'description' => 'The importance of fellowship and church community'],
            ['name' => 'Class 7 - Service', 'class_number' => 7, 'description' => 'Discovering your gifts and serving in the church'],
        ];

        foreach ($classes as $class) {
            FoundationClass::firstOrCreate(
                ['class_number' => $class['class_number']],
                $class
            );
        }
    }
}
