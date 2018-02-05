<?php

namespace App\Http\Controllers\MPE\QA;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\MPE\QA\StockRepository;
use App\Services\MPE\QA\StockService;
use Exception;
use App\Traits\Common;

class StockController extends Controller
{
    use Common;
    //
    private $stock;
    private $stockService;

    public function __construct(StockRepository $stock, StockService $stockService) {
        $this->stock = $stock;
        $this->stockService = $stockService;
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

    public function itemInfo($barcode) {
        try {
            $info = $this->stockService->getItemInfo($barcode);
            return response()->json($info, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }
}
