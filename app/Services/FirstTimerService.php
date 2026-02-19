<?php

namespace App\Services;

use App\Models\FirstTimer;
use App\Models\Church;
use App\Models\FoundationAttendance;
use App\Models\FoundationClass;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class FirstTimerService
{
    public function getForChurch($churchId, array $filters = [])
    {
        $query = FirstTimer::where('church_id', $churchId)
            ->with(['retainingOfficer', 'church']);

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

        return $query->latest('date_of_visit')->paginate(20);
    }

    public function getAll(array $filters = [])
    {
        $query = FirstTimer::with(['church.group.category', 'retainingOfficer']);

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

        return $query->latest('date_of_visit')->paginate(20);
    }

    public function create(array $data): FirstTimer
    {
        $data['created_by'] = Auth::id();

        // Auto-assign retaining officer from church if not specified
        if (empty($data['retaining_officer_id'])) {
            $church = Church::find($data['church_id']);
            $data['retaining_officer_id'] = $church?->retaining_officer_id;
        }

        return FirstTimer::create($data);
    }

    public function update(FirstTimer $firstTimer, array $data): FirstTimer
    {
        // Block updates if the first timer is already a Member (read-only)
        if ($firstTimer->status === 'Member') {
            throw new \Exception('Cannot update a member record. It is read-only.');
        }

        $data['updated_by'] = Auth::id();
        $firstTimer->update($data);
        return $firstTimer->fresh();
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

                // Auto-assign retaining officer from church
                $church = Church::find($churchId);
                $data['retaining_officer_id'] = $church?->retaining_officer_id;

                FirstTimer::create($data);
                $results['success']++;
            } catch (\Exception $e) {
                $results['errors'][] = "Row {$row}: {$e->getMessage()}";
            }
        }

        fclose($handle);
        return $results;
    }

    public function convertToMember(FirstTimer $firstTimer): FirstTimer
    {
        $firstTimer->update([
            'status' => 'Member',
            'updated_by' => Auth::id(),
        ]);
        return $firstTimer->fresh();
    }
}
