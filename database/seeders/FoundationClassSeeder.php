<?php

namespace Database\Seeders;

use App\Models\FoundationClass;
use Illuminate\Database\Seeder;

class FoundationClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            ['name' => 'Class 1 - The New Creature', 'class_number' => 1, 'description' => ''],
            ['name' => 'Class 2 - The Holy Spirit', 'class_number' => 2, 'description' => ''],
            ['name' => 'Class 3 - Christian Doctrines', 'class_number' => 3, 'description' => ''],
            ['name' => 'Class 4A - Evangelism', 'class_number' => 4, 'description' => ''],
            ['name' => 'Class 4B - Introduction to Cell Ministry', 'class_number' => 5, 'description' => ''],
            ['name' => 'Class 5 - Christisan Character and Prosperity', 'class_number' => 6, 'description' => ''],
            ['name' => 'Class 6 - The Local Assembly and Loveworld Inc. (Christ Embassy)', 'class_number' => 7, 'description' => ''],
            ['name' => 'Class 7 - Introduction to Mobile Technology for Personal Growth, Evangelism and Church Growth', 'class_number' => 8, 'description' => ''],
        ];

        foreach ($classes as $class) {
            FoundationClass::firstOrCreate(
                ['class_number' => $class['class_number']],
                $class
            );
        }
    }
}
