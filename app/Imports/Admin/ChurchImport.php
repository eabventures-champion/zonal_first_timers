<?php

namespace App\Imports\Admin;

use App\Models\Church;
use App\Models\ChurchGroup;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class ChurchImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $groupName = trim($row['group_name']);
            $churchName = trim($row['church_name']);
            $leaderName = trim($row['name_of_leader'] ?? '');
            $leaderContact = trim($row['contact_of_leader'] ?? '');
            $roName = trim($row['retaining_officer_name'] ?? '');
            $roContact = trim($row['retaining_officer_contact'] ?? '');

            // 1. Find the group
            $group = ChurchGroup::where('name', $groupName)->first();
            if (!$group) {
                // If group doesn't exist, we might want to skip or log an error.
                // For now, let's assume valid groups are provided.
                continue;
            }

            // 2. Handle Retaining Officer (User)
            $ro = null;
            if ($roContact) {
                $ro = User::where('phone', $roContact)->first();
                if (!$ro) {
                    $ro = User::create([
                        'name' => $roName ?: "RO for {$churchName}",
                        'phone' => $roContact,
                        'email' => $roContact . '@zonal.com', // Fallback email
                        'password' => $roContact, // Default password is phone
                    ]);
                }

                if (!$ro->hasRole('Retaining Officer')) {
                    $ro->assignRole('Retaining Officer');
                }
            }

            // 3. Create the Church
            $church = Church::create([
                'church_group_id' => $group->id,
                'name' => $churchName,
                'leader_name' => $leaderName,
                'leader_contact' => $leaderContact,
                'retaining_officer_id' => $ro ? $ro->id : null,
                'created_by' => Auth::id(),
            ]);

            // 4. Link User back to the Church if they were created/assigned
            if ($ro && !$ro->church_id) {
                $ro->update(['church_id' => $church->id]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'group_name' => 'required|exists:church_groups,name',
            'church_name' => 'required|unique:churches,name',
            'contact_of_leader' => 'nullable',
            'retaining_officer_contact' => 'nullable',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'group_name.exists' => 'The group ":input" does not exist.',
            'church_name.unique' => 'The church name ":input" is already taken.',
        ];
    }
}
