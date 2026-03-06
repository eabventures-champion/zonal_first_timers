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
            'Full Name',
            'Primary Contact',
            'Email',
            'Gender',
            'Residential Address',
            'Date of Visit',
            'Alternate Contact',
            'Date of Birth',
            'Occupation',
            'Marital Status',
            'Bringer Name',
            'Bringer Contact',
            'Born Again',
            'Water Baptism',
            'Church Event',
            'Prayer Requests'
        ];
    }
}
