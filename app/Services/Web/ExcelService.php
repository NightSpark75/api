<?php
namespace App\Services\Web;

use Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Exception;
use DB;

class ExcelService implements FromCollection 
{
    public $data;
    public function collection()
    {
        return $this->data;
    }

    public function download($data, $name, $array = false)
    {
        $this->data = $data;
        if ($array) $this->data = collect($data);
        return Excel::download($this, $name);
    }
}