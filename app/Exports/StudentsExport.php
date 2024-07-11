<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class  StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct(public Collection $records)
    {
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->records;
    }

    public function map($student): array
    {
        return [
            $student->name,
            $student->email,
            $student->class ? $student->class->name : 'NULL',
            $student->section ? $student->section->name : 'NULL',
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'class',
            'Section',
        ];
    }
}
