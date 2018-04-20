<?php
namespace App\Http\Controllers\ProductWarehouse;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\ProductWarehouse\Inventory;

class InvExport implements FromCollection
{
    public function __construct(Inventory $inv)
    {
        $this->inv = $inv;
    }

    public function collection()
    {
        return $this->inv->all();
    }
}