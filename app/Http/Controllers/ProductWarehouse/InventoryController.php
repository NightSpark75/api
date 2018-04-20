<?php
/**
 * inventory controller
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/04/19
 * @since 1.0.0 spark: build basic function
 * 
 */
namespace App\Http\Controllers\ProductWarehouse;

use App\Http\Controllers\Controller;
use App\Services\ProductWarehouse\InventoryService;
use Exception;
use App\Traits\Common;
use Excel;
use App\Http\Controllers\ProductWarehouse\InvExport;

/**
 * Class InventoryController
 *
 * @package App\Http\Controllers\ProductWarehouse
 */
class InventoryController extends Controller
{
    use Common;

    /**
     * @var InventoryService
     */
    private $inventoryService;

    /**
     * @param InventoryService $inventoryService
     * @throws Exception
     */
    public function __construct(InventoryService $inventoryService) 
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * get inventory list
     *
     * @param string $date
     * @throws Exception
     * @return mixed
     */
    public function getInventoryList($date = null)
    {
        try {
            $list = $this->inventoryService->getInventoryList($date);
            return response()->json($list, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * get inventory item
     *
     * @param string $cyno
     * @throws Exception
     * @return mixed
     */
    public function getInventoryItem($cyno)
    {
        try {
            $item = $this->inventoryService->getInventoryItem($cyno);
            return response()->json($item, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * save inventory amount
     *
     * @throws Exception
     * @return mixed
     */
    public function saveInventory()
    {
        try {
            $user = session('user');
            $id = $user->id;
            $cyno = request()->input('cyno');
            $locn = request()->input('locn');
            $litm = request()->input('litm');
            $lotn = request()->input('lotn');
            $amount = request()->input('amount');
            $nextItem = $this->inventoryService->saveInventory($id, $cyno, $locn, $litm, $lotn, $amount);
            return response()->json(['item' => $nextItem], 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * get all inventory data
     * 
     * @param string $cyno
     * @return mixed
     */
    public function all($cyno) 
    {
        try {
            $user = session('user');
            $id = $user->id;
            $inventory = $this->inventoryService->saveInventory($id, $cyno);
            return response()->json(['inventory' => $inventory], 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * export inventory data to excel
     * 
     * @return response
     */
    public function export(Excel $excel, InvExport $export)
    {
        $cyno = request()->input('cyno');
        $cellData = [
            ['学号','姓名','成绩'],
            ['10001','AAAAA','99'],
            ['10002','BBBBB','92'],
            ['10003','CCCCC','95'],
            ['10004','DDDDD','89'],
            ['10005','EEEEE','96'],
        ];
        //return Excel::download($cellData, 'invoices.xlsx');
        //return $this->excel->export(new Export);
        //return Excel::download($cellData, 'invoices.xlsx');
        return Excel::download($export, 'invoices.xlsx');
    }
}
