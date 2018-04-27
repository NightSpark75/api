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
use App\Services\Web\ExcelService;

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
     * @var ExcelService
     */
    private $excel;

    /**
     * @param InventoryService $inventoryService
     * @param ExcelService $excel
     */
    public function __construct(
        InventoryService $inventoryService,
        ExcelService $excel
    ) {
        $this->inventoryService = $inventoryService;
        $this->excel = $excel;
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
            $user = session('user');
            $id = $user->id;
            $list = $this->inventoryService->getInventoryList($id, $date);
            return response()->json($list, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * check inventory is finished or not
     *
     * @param string $cyno
     * @throws Exception
     * @return mixed
     */
    public function checkFinished($cyno)
    {
        try {
            $finished = $this->inventoryService->checkFinished($cyno);
            return response()->json($finished, 200);
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
            $user = session('user');
            $id = $user->id;
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
            return response()->json($nextItem, 200);
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
    public function inventoried($cyno) 
    {
        try {
            $user = auth()->user();
            $id = $user->id;
            $inventoried = $this->inventoryService->getInventoried($id, $cyno);
            if (count($inventoried) === 0) {
                return response()->json(['msg' => '無盤點資料!'], 401);
            }
            return response()->json(['inventoried' => $inventoried], 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * export inventory data to excel
     * 
     * @return response
     */
    public function export($cyno)
    {
        $id = auth()->user()->id;
        $inventoried = $this->inventoryService->getExportData($id, $cyno);
        $export = $this->excel->download($inventoried, $cyno.'盤點資料.xlsx', true);
        return $export;
    }

    public function startInventory()
    {
        try {
            $user = session('user');
            $id = $user->id;
            $cyno = request()->input('cyno');
            $this->inventoryService->startInventory($cyno, $id);
            return response()->json([], 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    public function pauseInventory()
    {
        try {
            $user = session('user');
            $id = $user->id;
            $cyno = request()->input('cyno');
            $this->inventoryService->pauseInventory($cyno, $id);
            return response()->json([], 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    public function endInventory()
    {
        try {
            $user = session('user');
            $id = $user->id;
            $cyno = request()->input('cyno');
            $this->inventoryService->endInventory($cyno, $id);
            return response()->json([], 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }
}
