<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChurchCategory;
use App\Models\ChurchGroup;
use App\Models\Church;

class ChurchHierarchySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Categories
        $categories = [
            [
                'id' => 1,
                'name' => 'MAIN CHURCH',
                'zonal_pastor_name' => 'Highly Esteemed Pastor Lisa Ma',
                'zonal_pastor_contact' => null,
                'created_by' => 1,
            ],
            [
                'id' => 2,
                'name' => 'OTHER CHURCHES',
                'zonal_pastor_name' => 'Highly Esteemed Pastor Lisa Ma',
                'zonal_pastor_contact' => null,
                'created_by' => 1,
            ]
        ];

        foreach ($categories as $category) {
            ChurchCategory::updateOrCreate(['id' => $category['id']], array_merge($category, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 2. Groups
        $groups = [
            [
                'id' => 1,
                'church_category_id' => 1,
                'name' => 'AVENOR',
                'pastor_name' => 'Highly Esteemed Pastor Lisa Ma',
                'pastor_contact' => null,
                'created_by' => 1,
            ],
            [
                'id' => 2,
                'church_category_id' => 2,
                'name' => 'ZONAL CHURCH GROUP 1',
                'pastor_name' => 'Highly Esteemed Pastor Lisa Ma',
                'pastor_contact' => null,
                'created_by' => 1,
            ],
            [
                'id' => 3,
                'church_category_id' => 2,
                'name' => 'STRATEGIC GROUP 1',
                'pastor_name' => 'Pastor Chidinma David',
                'pastor_contact' => '0241548204',
                'created_by' => 1,
            ]
        ];

        foreach ($groups as $group) {
            ChurchGroup::updateOrCreate(['id' => $group['id']], array_merge($group, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 3. Churches
        $churches = [
            [
                'id' => 1,
                'church_group_id' => 1,
                'name' => 'CHOSEN',
                'leader_name' => 'Bro Fritz Mawusi Ackumey',
                'leader_contact' => '0547977840',
                'retaining_officer_id' => null, // Set to null per user request
                'created_by' => 1,
            ],
            [
                'id' => 2,
                'church_group_id' => 3,
                'name' => 'CE TAIFA',
                'leader_name' => 'Pastor Chidinma David',
                'leader_contact' => '0241548204',
                'retaining_officer_id' => null, // Set to null per user request
                'created_by' => 1,
            ],
            [
                'id' => 3,
                'church_group_id' => 2,
                'name' => 'CE DOME',
                'leader_name' => 'Dcn. Kofi Affum',
                'leader_contact' => '0246868672',
                'retaining_officer_id' => null,
                'created_by' => 1,
            ],
            [
                'id' => 4,
                'church_group_id' => 2,
                'name' => 'CE NORTH KANESHIE',
                'leader_name' => 'Dr. Emmanuel Arthur',
                'leader_contact' => '0545282656',
                'retaining_officer_id' => null,
                'created_by' => 1,
            ],
            [
                'id' => 5,
                'church_group_id' => 2,
                'name' => 'CE COMMUNITY 18 TEMA',
                'leader_name' => 'Sis Philomena Edusei',
                'leader_contact' => '0244539889',
                'retaining_officer_id' => null,
                'created_by' => 1,
            ],
            [
                'id' => 6,
                'church_group_id' => 2,
                'name' => 'CE KPONE',
                'leader_name' => 'Sis Ahiati Annie',
                'leader_contact' => '0245250192',
                'retaining_officer_id' => null,
                'created_by' => 1,
            ],
            [
                'id' => 7,
                'church_group_id' => 2,
                'name' => 'CE KINSBY',
                'leader_name' => 'Bro Evans Appau-Baffour',
                'leader_contact' => '0265202657',
                'retaining_officer_id' => null,
                'created_by' => 1,
            ]
        ];

        foreach ($churches as $church) {
            Church::updateOrCreate(['id' => $church['id']], array_merge($church, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
