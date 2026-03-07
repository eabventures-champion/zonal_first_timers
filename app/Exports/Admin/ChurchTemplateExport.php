<?php

namespace App\Exports\Admin;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ChurchTemplateExport implements FromCollection, WithHeadings
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
            'Group Name',
            'Church Name',
            'Name of leader',
            'Contact of leader',
            'Retaining Officer Name',
            'Retaining Officer Contact',
        ];
    }
}
