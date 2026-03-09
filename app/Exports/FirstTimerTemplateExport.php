<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FirstTimerTemplateExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect([]);
    }

    public function headings(): array
    {
        return [
            'Date of Visit',
            'Group Church',
            'Church',
            'Full Name',
            'Primary Contact',
            'Gender',
            'Birthday',
            'Occupation',
            'Marital Status',
            'Church Event',
            'Residential Address',
            'Bringer Name',
            'Bringer Contact',
            'Born Again',
            'Water Baptism',
            'Email',
            'Alternate Contact',
            'Prayer Requests'
        ];
    }
}
