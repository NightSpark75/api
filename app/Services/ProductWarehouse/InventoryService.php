<?php
/**
 * inventory service
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/04/19
 * @since 1.0.0 spark: handle inventory business logic
 * 
 */
namespace App\Services\ProductWarehouse;

use App\Repositories\ProductWarehouse\InventoryRepository;
use App\Services\Web\ExcelService;
use Exception;
use DB;

/**
 * Class InventoryService
 *
 * @package App\Services
 */
class InventoryService {

    /**
     * @var InventoryRepository
     */
    private $inventoryRepository;

    /**
     * @var ExcelService
     */
    private $excel;

    /**
     * @param InventoryRepository $inventoryRepository
     * @param ExcelService $excel
     */
    public function __construct(
        InventoryRepository $inventoryRepository,
        ExcelService $excel
    ) {
        $this->inventoryRepository = $inventoryRepository;
        $this->excel = $excel;
    }

    public function getInventoryList($date)
    {
        $list = $this->inventoryRepository->getInventoryList($date);
        return $list;
    }

    public function getInventoryItem($cyno)
    {
        $item = $this->inventoryRepository->getInventoryItem($cyno);
        return $item;
    }

    public function checkFinished($cyno)
    {
        $finished = $this->inventoryRepository->checkFinished($cyno);
        return $finished;
    }

    public function saveInventory($id, $cyno, $locn, $litm, $lotn, $amount)
    {
        $item = $this->inventoryRepository->getInventoryItem($cyno);
        if (!$item) return false;
        if (
            $item->locn === $locn &&
            $item->litm === $litm &&
            $item->lotn === $lotn
        ) {
            $this->inventoryRepository->saveInventory($id, $cyno, $locn, $litm, $lotn, $amount);
        }
        $finished = $this->inventoryRepository->checkFinished($cyno);
        if (!$finished) {    
            $nextItem = $this->inventoryRepository->getInventoryItem($cyno);
        } else {
            $nextItem = false;
        }
        return $nextItem;
    }

    public function getInventoried($id, $cyno)
    {
        $check = $this->inventoryRepository->checkInventoryUser($id, $cyno);
        if (!$check) throw new Exception('您沒有此盤點單號的權限!');
        $inventoried = $this->inventoryRepository->inventoried($cyno);
        return $inventoried;
    }
    
    public function export($id, $cyno)
    {
        $inventory = [];
        $check = $this->inventoryRepository->checkInventoryUser($id, $cyno);
        if ($check) $inventory = $this->inventoryRepository->export($cyno);
        return $this->excel->download($inventory, $cyno.'盤點資料.xlsx', true);
    }
}

