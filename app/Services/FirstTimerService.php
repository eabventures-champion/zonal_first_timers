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
        $data['created_by'] = Auth::id();

        $firstTimer = DB::transaction(function () use ($data) {
            // Handle Bringer logic
            $bringerId = $data['bringer_id'] ?? null;

            if (!$bringerId) {
                $bringerName = $data['bringer_name'] ?? null;
                $bringerContact = $data['bringer_contact'] ?? null;
                $churchId = $data['church_id'];

                if ($bringerName && $bringerContact) {
                    // Find or create person by contact (ensuring uniqueness)
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

            // Create User account for the First Timer
            $user = User::create([
                'name' => $firstTimer->full_name,
                'email' => $firstTimer->email ?? ($firstTimer->primary_contact . '@church.com'),
                'phone' => $firstTimer->primary_contact,
                'password' => $firstTimer->primary_contact,
                'church_id' => $firstTimer->church_id,
            ]);
            $user->assignRole('Member');

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
        $results = ['success' => 0, 'errors' => []];

        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle);

        // Normalize header keys
        $header = array_map(fn($h) => strtolower(trim(str_replace(' ', '_', $h))), $header);

        $row = 1;
        while (($line = fgetcsv($handle)) !== false) {
            $row++;
            try {
                $data = array_combine($header, $line);
                $data['church_id'] = $churchId;
                $data['born_again'] = strtolower($data['born_again'] ?? 'no') === 'yes';
                $data['water_baptism'] = strtolower($data['water_baptism'] ?? 'no') === 'yes';
                $data['status'] = $data['status'] ?? 'New';
                $data['created_by'] = Auth::id();

                $church = Church::find($churchId);
                $data['retaining_officer_id'] = $church?->retaining_officer_id;

                $this->create($data);

                $results['success']++;
            } catch (\Exception $e) {
                $results['errors'][] = "Row {$row}: {$e->getMessage()}";
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
}
