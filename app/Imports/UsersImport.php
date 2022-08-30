<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

//use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersImport implements FromCollection,WithHeadings
{
    use Exportable;

    public function __construct($data)
    {
        $this->data = $data['data'];
        $this->days = $data['days'];
    }

    public function collection()
    {
        return collect($this->data);
    }
    public function headings(): array
    {
        return $this->days;
    }
}
