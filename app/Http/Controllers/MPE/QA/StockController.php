<?php

namespace App\Http\Controllers\MPE\QA;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\MPE\QA\StockRepository;

class StockController extends Controller
{
    //
    private $stock;

    public function __construct(StockRepository $stock) {
        $this->stock = $stock;
        $this->program = 'MPEW0020';
        session(['program' => $this->program]);
        $this->middleware('role');
    }

    public function getStockList($str = null) {
        $result = $this->stock->getStockList($str);
        return $result;
    }

    public function storageChange() {
        $input = request()->all();
        $result = $this->stock->storageChange($input);
        return $result;
    }
}
