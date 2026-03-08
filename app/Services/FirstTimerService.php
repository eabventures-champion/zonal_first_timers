<?php

namespace App\Services;

use App\Models\FirstTimer;
use App\Models\Member;
use App\Models\Church;
use App\Models\FoundationAttendance;
use App\Models\FoundationClass;
use App\Models\WeeklyAttendance;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class FirstTimerService
{
    public function getForChurch($churchId, array $filters = [])
    {
        $filters['church_id'] = $churchId;
        return $this->getAll($filters);
    }

    public function getAll(array $filters = [], bool $paginate = true)
    {
        $query = FirstTimer::with([
            'church.group.category',
            'retainingOfficer',
            'weeklyAttendances' => fn($q) => $q->where('attended', true)->orderByDesc('service_date'),
            'foundationAttendances.foundationClass'
        ])->withCount(['weeklyAttendances as total_attended' => fn($q) => $q->where('attended', true)]);

        return $this->applyFilters($query, $filters, $paginate);
    }

    public function getMembers(array $filters = [], bool $paginate = true)
    {
        $query = Member::with([
            'church.group.category',
            'retainingOfficer',
            'weeklyAttendances' => fn($q) => $q->where('attended', true)->orderByDesc('service_date'),
            'foundationAttendances.foundationClass'
        ])->withCount(['weeklyAttendances as total_attended' => fn($q) => $q->where('attended', true)]);

        return $this->applyFilters($query, $filters, $paginate);
    }

    private function applyFilters($query, array $filters = [], bool $paginate = true)
    {
        if (!empty($filters['church_id'])) {
            $query->where('church_id', $filters['church_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('full_name', 'like', "%{$filters['search']}%")
                    ->orWhere('email', 'like', "%{$filters['search']}%")
                    ->orWhere('primary_contact', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['date_from'])) {
            $query->where('date_of_visit', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('date_of_visit', '<=', $filters['date_to']);
        }

        $query->latest('date_of_visit');

        return $paginate ? $query->paginate(20) : $query->get();
    }

    public function create(array $data): FirstTimer
    {
        // Prevent registering existing administrative users as first timers
        $existingUser = User::where('phone', $data['primary_contact'])->first();
        if ($existingUser && $existingUser->roles()->whereIn('name', ['Super Admin', 'Admin', 'Retaining Officer'])->exists()) {
            throw new \Exception("This phone number belongs to an existing " . $existingUser->getRoleNames()->first() . " and cannot be registered as a first timer.");
        }

        $data['created_by'] = Auth::id();

        $firstTimer = DB::transaction(function () use ($data) {
            // Handle Bringer logic
            $bringerId = $data['bringer_id'] ?? null;

            if (!$bringerId) {
                $bringerName = $data['bringer_name'] ?? null;
                $bringerContact = $data['bringer_contact'] ?? null;
                $churchId = $data['church_id'];

                if ($bringerName && $bringerContact) {
                    $churchId = $data['church_id'];

                    // Bringer-Church Restriction: Ensure bringer doesn't belong to another church
                    $existingBringer = \App\Models\Bringer::where('contact', $bringerContact)->first();
                    if ($existingBringer && $existingBringer->church_id != $churchId) {
                        $existingChurch = $existingBringer->church->name ?? 'another church';
                        throw new \Exception("The bringer '{$existingBringer->name}' (Contact: {$bringerContact}) is already registered with '{$existingChurch}'. A bringer cannot be associated with multiple churches.");
                    }

                    // Find or create person by contact
                    $bringer = \App\Models\Bringer::updateOrCreate(
                        ['contact' => $bringerContact],
                        [
                            'church_id' => $churchId,
                            'name' => $bringerName,
                        ]
                    );

                    // Ensure Bringer has a User account for login
                    if (!$bringer->user_id) {
                        $user = User::where('phone', $bringerContact)->first();

                        // If no user exists, check if they are a first timer or member to get their name
                        if (!$user) {
                            $member = \App\Models\Member::where('primary_contact', $bringerContact)->first();
                            $ft = \App\Models\FirstTimer::where('primary_contact', $bringerContact)->first();

                            $finalName = $bringerName;
                            if ($member)
                                $finalName = $member->full_name;
                            elseif ($ft)
                                $finalName = $ft->full_name;

                            $user = User::create([
                                'name' => $finalName,
                                'phone' => $bringerContact,
                                'email' => $bringerContact . '@zonal.com',
                                'password' => $bringerContact,
                                'church_id' => $churchId,
                            ]);
                        }

                        if (!$user->hasRole('Bringer')) {
                            $user->assignRole('Bringer');
                        }

                        // Ensure they have the Member role if they were a First Timer or Member record match
                        // (Though lines 164-166 handle this for the NEW first timer, we should check here for the BRINGER)
                        $isFTorMember = \App\Models\Member::where('primary_contact', $bringerContact)->exists() ||
                            \App\Models\FirstTimer::where('primary_contact', $bringerContact)->exists();

                        if ($isFTorMember && !$user->hasRole('Member') && !$user->isAdminStaff()) {
                            $user->assignRole('Member');
                        }

                        $bringer->update(['user_id' => $user->id]);
                    } else {
                        // Even if bringer record exists with user_id, ensure the User has the Bringer role
                        $user = $bringer->user;
                        if ($user && !$user->hasRole('Bringer')) {
                            $user->assignRole('Bringer');
                        }
                    }
                    $bringerId = $bringer->id;
                } else {
                    // Fallback to Retaining Officer details
                    $church = Church::find($churchId);
                    $officerId = $data['retaining_officer_id'] ?? $church?->retaining_officer_id;

                    if ($officerId) {
                        $officer = \App\Models\User::find($officerId);
                        if ($officer) {
                            $bringer = \App\Models\Bringer::firstOrCreate(
                                ['church_id' => $churchId, 'user_id' => $officer->id],
                                [
                                    'name' => $officer->name,
                                    'contact' => $officer->phone,
                                    'is_ro' => true
                                ]
                            );
                            $bringerId = $bringer->id;
                        }
                    }
                }
            }

            $data['bringer_id'] = $bringerId;
            $firstTimer = FirstTimer::create($data);

            // Create or find User account for the First Timer
            $user = User::firstOrCreate(
                ['phone' => $firstTimer->primary_contact],
                [
                    'name' => $firstTimer->full_name,
                    'email' => $firstTimer->email ?? ($firstTimer->primary_contact . '@church.com'),
                    'password' => $firstTimer->primary_contact,
                    'church_id' => $firstTimer->church_id,
                ]
            );
            if (!$user->hasRole('Member') && !$user->isAdminStaff()) {
                $user->assignRole('Member');
            }

            $firstTimer->update(['user_id' => $user->id]);

            // Auto-record initial attendance
            $this->recordInitialAttendance($firstTimer);

            return $firstTimer;
        });

        return $firstTimer;
    }

    private function recordInitialAttendance(FirstTimer $firstTimer): void
    {
        if (!$firstTimer->date_of_visit) {
            return;
        }

        $date = Carbon::parse($firstTimer->date_of_visit);
        $weekNumber = WeeklyAttendance::getWeekNumberForDate($date);

        WeeklyAttendance::updateOrCreate(
            [
                'first_timer_id' => $firstTimer->id,
                'church_id' => $firstTimer->church_id,
                'week_number' => $weekNumber,
                'month' => $date->month,
                'year' => $date->year,
            ],
            [
                'service_date' => $date,
                'attended' => true,
                'notes' => 'Initial visit attendance',
                'recorded_by' => Auth::id() ?? $firstTimer->created_by,
            ]
        );
    }

    private function syncDateOfVisit(FirstTimer $firstTimer): void
    {
        $earliestAttendance = $firstTimer->weeklyAttendances()
            ->where('attended', true)
            ->oldest('service_date')
            ->first();

        if ($earliestAttendance && $earliestAttendance->service_date) {
            $attendanceDate = Carbon::parse($earliestAttendance->service_date)->startOfDay();
            $currentVisitDate = Carbon::parse($firstTimer->date_of_visit)->startOfDay();

            // If we found an earlier attendance than the current visit date, update it
            if ($attendanceDate->lt($currentVisitDate)) {
                $firstTimer->update(['date_of_visit' => $attendanceDate->toDateString()]);
            }
        }
    }

    public function update(FirstTimer $firstTimer, array $data): FirstTimer
    {
        return DB::transaction(function () use ($firstTimer, $data) {
            $data['updated_by'] = Auth::id();

            // Handle Bringer logic
            $bringerId = $data['bringer_id'] ?? null;

            if (!$bringerId) {
                $bringerName = $data['bringer_name'] ?? null;
                $bringerContact = $data['bringer_contact'] ?? null;
                $churchId = $data['church_id'] ?? $firstTimer->church_id;

                if ($bringerName && $bringerContact) {
                    $bringer = \App\Models\Bringer::updateOrCreate(
                        ['contact' => $bringerContact],
                        [
                            'church_id' => $churchId,
                            'name' => $bringerName,
                        ]
                    );

                    // Ensure Bringer has a User account for login
                    if (!$bringer->user_id) {
                        $user = User::where('phone', $bringerContact)->first();
                        if (!$user) {
                            $user = User::create([
                                'name' => $bringerName,
                                'phone' => $bringerContact,
                                'email' => $bringerContact . '@zonal.com',
                                'password' => $bringerContact,
                                'church_id' => $churchId,
                            ]);
                        }
                        if (!$user->hasRole('Bringer')) {
                            $user->assignRole('Bringer');
                        }
                        $bringer->update(['user_id' => $user->id]);
                    }
                    $bringerId = $bringer->id;
                } else {
                    // Fallback to Retaining Officer if not provided and not explicitly selecting a person
                    // However, for updates, we might want to keep the existing one if none provided
                    // But if they cleared the manual fields AND didn't select anyone, maybe fallback to RO?
                    // Actually, let's mirror create logic for consistency.
                }
            }

            if ($bringerId) {
                $data['bringer_id'] = $bringerId;
            }

            $firstTimer->update($data);

            // Sync linked User record if it exists
            if ($firstTimer->user_id) {
                $userUpdates = [];
                if (isset($data['full_name'])) {
                    $userUpdates['name'] = $data['full_name'];
                }
                if (isset($data['email'])) {
                    $userUpdates['email'] = $data['email'];
                } elseif (isset($data['primary_contact'])) {
                    // If email wasn't explicitly set but contact changed, update the fallback email
                    $user = User::find($firstTimer->user_id);
                    if ($user && str_ends_with($user->email, '@church.com')) {
                        $userUpdates['email'] = $data['primary_contact'] . '@church.com';
                    }
                }
                if (isset($data['primary_contact'])) {
                    $userUpdates['phone'] = $data['primary_contact'];
                }
                if (isset($data['church_id'])) {
                    $userUpdates['church_id'] = $data['church_id'];
                }
                if (!empty($userUpdates)) {
                    User::where('id', $firstTimer->user_id)->update($userUpdates);
                }
            }

            return $firstTimer->fresh();
        });
    }

    public function delete(FirstTimer $firstTimer): void
    {
        $firstTimer->delete();
    }

    public function importFromCsv(UploadedFile $file, int $churchId): array
    {
        ini_set('auto_detect_line_endings', true);
        $results = ['success' => 0, 'errors' => []];

        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle);

        if (!$header) {
            return ['success' => 0, 'errors' => ['The CSV file is empty or invalid.']];
        }

        // Normalize header keys
        $header = array_map(fn($h) => strtolower(trim(str_replace([' ', "\r", "\n"], ['_', '', ''], $h))), $header);

        \Illuminate\Support\Facades\Log::info("Starting CSV import for church {$churchId}. File: " . $file->getClientOriginalName());

        $row = 1;
        while (($line = fgetcsv($handle)) !== false) {
            $row++;

            // Skip empty rows
            if (empty(array_filter($line))) {
                continue;
            }

            if ($row === 2) {
                \Illuminate\Support\Facades\Log::info("Sample data from Row 2: " . implode(', ', $line));
            }

            $currentData = [];
            try {
                // Ensure column count matches header
                if (count($header) !== count($line)) {
                    $results['errors'][] = "Row {$row}: Column count mismatch. Expected " . count($header) . " columns, but found " . count($line) . ".";
                    continue;
                }

                $currentData = array_combine($header, $line);

                // Trim and nullify empty strings
                foreach ($currentData as $key => $val) {
                    $val = trim((string) $val);
                    $currentData[$key] = $val === '' ? null : $val;
                }

                $currentData['church_id'] = $churchId;
                $currentData['born_again'] = strtolower($currentData['born_again'] ?? 'no') === 'yes';
                $currentData['water_baptism'] = strtolower($currentData['water_baptism'] ?? 'no') === 'yes';
                $currentData['status'] = $currentData['status'] ?? 'New';
                $currentData['created_by'] = Auth::id();

                $church = Church::find($churchId);
                $currentData['retaining_officer_id'] = $church?->retaining_officer_id;

                $currentData['date_of_visit'] = $this->parseDate($currentData['date_of_visit'] ?? null);
                if (isset($currentData['date_of_birth']) && $currentData['date_of_birth']) {
                    $currentData['date_of_birth'] = $this->parseDate($currentData['date_of_birth']);
                } else {
                    unset($currentData['date_of_birth']);
                }

                // Bringer-Church Restriction check for import
                $bringerContact = $currentData['bringer_contact'] ?? null;
                if ($bringerContact) {
                    $existingBringer = \App\Models\Bringer::where('contact', $bringerContact)->first();
                    if ($existingBringer && $existingBringer->church_id != $churchId) {
                        $existingChurch = $existingBringer->church->name ?? 'another church';
                        $results['errors'][] = "Row {$row}: The bringer '{$existingBringer->name}' (Contact: {$bringerContact}) is already registered with '{$existingChurch}'. A bringer cannot be associated with multiple churches.";
                        continue;
                    }
                }

                // Row-level validation
                $validator = Validator::make($currentData, [
                    'full_name' => 'required|string|max:255',
                    'primary_contact' => [
                        'required',
                        'string',
                        'min:10',
                        'max:20',
                        Rule::unique('first_timers', 'primary_contact'),
                        Rule::unique('members', 'primary_contact'),
                        Rule::unique('users', 'phone'),
                    ],
                    'email' => [
                        'nullable',
                        'email',
                        Rule::unique('first_timers', 'email'),
                        Rule::unique('members', 'email'),
                        Rule::unique('users', 'email'),
                    ],
                    'gender' => 'required|in:Male,Female',
                    'residential_address' => 'required|string|max:1000',
                    'date_of_visit' => 'required|date',
                ], [
                    'primary_contact.unique' => 'Phone number :input already registered.',
                    'email.unique' => 'Email :input already registered.',
                    'gender.in' => 'Gender must be Male or Female (got ":input").',
                ]);

                if ($validator->fails()) {
                    foreach ($validator->errors()->getMessages() as $field => $messages) {
                        foreach ($messages as $message) {
                            if (str_contains($message, 'already registered')) {
                                $results['errors'][] = "Row {$row}: " . $this->humanizeDuplicate($field, $currentData[$field]);
                            } else {
                                $results['errors'][] = "Row {$row}: {$message}";
                            }
                        }
                    }
                    continue;
                }

                $this->create($currentData);
                $results['success']++;

            } catch (\Throwable $e) {
                $results['errors'][] = "Row {$row}: " . $this->humanizeError($e, $currentData);
            }
        }

        fclose($handle);
        return $results;
    }

    public function convertToMember(FirstTimer $firstTimer): Member
    {
        return DB::transaction(function () use ($firstTimer) {
            $data = $firstTimer->toArray();

            // Ensure timestamps are set and status is Retained
            $data['status'] = 'Retained';
            $data['membership_approved_at'] = now();
            $data['migrated_at'] = now();
            $data['updated_by'] = Auth::id();
            $data['user_id'] = $firstTimer->user_id;

            // Create member record
            $member = Member::create($data);

            // Migrate Weekly Attendances
            $firstTimer->weeklyAttendances()->update([
                'member_id' => $member->id,
                'first_timer_id' => null
            ]);

            // Migrate Foundation Attendances
            $firstTimer->foundationAttendances()->update([
                'member_id' => $member->id,
                'first_timer_id' => null
            ]);

            // Delete first timer record
            $firstTimer->delete();

            return $member;
        });
    }

    public function syncMembershipStatus(FirstTimer $firstTimer): void
    {
        \Log::info("syncMembershipStatus called for FT #{$firstTimer->id}");
        // Sync date_of_visit with earliest attendance before status check
        $this->syncDateOfVisit($firstTimer);

        $attendedCount = $firstTimer->weeklyAttendances()->where('attended', true)->count();

        if ($attendedCount >= 6) {
            $firstTimer->update([
                'status' => 'Retained',
                'membership_requested_at' => $firstTimer->membership_requested_at ?? now(),
                'membership_approved_at' => $firstTimer->membership_approved_at ?? now(),
                'updated_by' => Auth::id(),
            ]);

            // Auto-convert to Member move them to dedicated table
            $this->convertToMember($firstTimer);
        } elseif ($attendedCount >= 2) {
            $firstTimer->update(['status' => 'Developing']);
        } else {
            $firstTimer->update(['status' => 'New']);
        }
    }

    public function getPendingApprovals(): Collection
    {
        return Member::whereNull('acknowledged_at')
            ->with([
                'church',
                'retainingOfficer',
                'weeklyAttendances' => fn($q) => $q->where('attended', true)->orderByDesc('service_date'),
                'foundationAttendances.foundationClass'
            ])
            ->orderBy('membership_approved_at')
            ->get();
    }

    public function acknowledgeMembership(Member $member): Member
    {
        $member->update(['acknowledged_at' => now()]);
        return $member;
    }

    public function bulkAcknowledge(array $memberIds): int
    {
        return Member::whereIn('id', $memberIds)
            ->whereNull('acknowledged_at')
            ->update(['acknowledged_at' => now()]);
    }

    public function bulkSyncMembershipStatus(?int $churchId = null): int
    {
        $query = FirstTimer::where('status', '!=', 'Retained')
            ->whereNull('membership_requested_at');

        if ($churchId) {
            $query->where('church_id', $churchId);
        }

        $count = 0;
        $query->chunk(100, function ($firstTimers) use (&$count) {
            foreach ($firstTimers as $ft) {
                /** @var \App\Models\FirstTimer $ft */
                $this->syncMembershipStatus($ft);
                $count++;
            }
        });

        return $count;
    }

    private function humanizeError(\Throwable $e, array $data): string
    {
        $message = $e->getMessage();

        // Handle DB integrity constraints (duplicates)
        if (str_contains($message, '1062 Duplicate entry')) {
            // Check for the unique index name rather than just the column name, 
            // since the exception message includes the full SQL query which contains all column names.
            if (str_contains($message, 'primary_contact_unique')) {
                return $this->humanizeDuplicate('primary_contact', $data['primary_contact'] ?? 'Unknown');
            }
            if (str_contains($message, 'alternate_contact_unique') || str_contains($message, 'first_timers_alternate_contact_unique')) {
                return "The alternate contact '{$data['alternate_contact']}' is already in use by another person.";
            }
            if (str_contains($message, 'email_unique')) {
                return $this->humanizeDuplicate('email', $data['email'] ?? 'Unknown');
            }
            if (str_contains($message, 'users_phone_unique')) {
                return "The contact number '{$data['primary_contact']}' is already linked to an existing user account.";
            }
            return "This record contains duplicate information that already exists.";
        }

        // Handle specific role check from create method
        if (str_contains($message, 'belongs to an existing')) {
            return $message;
        }

        // Generic technical error catcher
        return "System error: " . (str_contains($message, 'SQLSTATE') ? "Invalid data format or database restriction." : $message);
    }

    private function humanizeDuplicate(string $field, $value): string
    {
        $existing = FirstTimer::where($field, $value)->first()
            ?? Member::where('primary_contact', $value)->first();

        if (!$existing && ($field === 'primary_contact' || $field === 'email')) {
            $userField = ($field === 'primary_contact') ? 'phone' : 'email';
            $existingUser = User::where($userField, $value)->first();

            if ($existingUser) {
                $role = $existingUser->getRoleNames()->first() ?? 'User';
                return "The {$field} '{$value}' is already registered to an existing system user: '{$existingUser->name}' ({$role}).";
            }
        }

        if ($existing) {
            $churchName = $existing->church?->name ?? 'Unknown Church';
            $bringerName = $existing->bringer?->name ?? 'Unknown Bringer';
            $type = ($existing instanceof Member) ? 'Member' : 'First Timer';
            $label = ($field === 'primary_contact' || $field === 'alternate_contact') ? 'Phone number' : 'Email';

            return "{$label} '{$value}' for {$existing->full_name} is already registered as a {$type} in {$churchName} (Brought by: {$bringerName}).";
        }

        return "{$field} '{$value}' is already in our system (Detailed record could not be found).";
    }

    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        // Handle DD/MM/YYYY format specifically if it contains slashes
        if (str_contains($date, '/')) {
            try {
                // Force interpretation as d/m/Y
                return Carbon::createFromFormat('d/m/Y', $date)->startOfDay();
            } catch (\Exception $e) {
                // Fallback to standard parsing if specific format fails
            }
        }

        try {
            return Carbon::parse($date)->startOfDay();
        } catch (\Exception $e) {
            return null;
        }
    }
}
