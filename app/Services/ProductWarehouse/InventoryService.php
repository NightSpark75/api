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
     * @param InventoryRepository $inventoryRepository
     */
    public function __construct(InventoryRepository $inventoryRepository) 
    {
        $this->inventoryRepository = $inventoryRepository;
    }

    /**
     * get inventory list
     * 
     * @param string $date
     * @return array
     */
    public function getInventoryList($user, $date)
    {
        $list = [];
        $current = $this->inventoryRepository->getCurrent($user);
        if (!$current) {
            $list = $this->inventoryRepository->getInventoryList($date);
        }
        return compact('current', 'list');
    }

    /**
     * get inventory item
     * 
     * @param string $cyno
     * @return array
     */
    public function getInventoryItem($cyno)
    {
        $item = $this->inventoryRepository->getInventoryItem($cyno);
        return $item;
    }

    /**
     * check inventory is finished or not
     * 
     * @param string $cyno
     * @return bool
     */
    public function checkFinished($cyno)
    {
        $finished = $this->inventoryRepository->checkFinished($cyno);
        return $finished;
    }

    /** 
     * save inventory data, and return next item
     * 
     * @param string $id
     * @param string $cyno
     * @param string $locn
     * @param string @litm
     * @param string $lotn
     * @param int $amount
     * @return stdClass
     */
    public function saveInventory($id, $cyno, $locn, $litm, $lotn, $amount)
    {
        $item = $this->inventoryRepository->getInventoryItem($cyno);
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
            $nextItem = null;
        }
        return $nextItem;
    }

    /**
     * get is inventoried data 
     * 
     * @param string $id
     * @param string $cyno
     * @return mixed
     */
    public function getInventoried($id, $cyno)
    {
        $check = $this->inventoryRepository->checkInventoryUser($id, $cyno);
        if (!$check) throw new Exception('您沒有此盤點單號的權限!');
        $inventoried = $this->inventoryRepository->inventoried($cyno);
        return $inventoried;
    }
    
    /**
     * check user auth and return inventoried data
     * 
     * @param string $id
     * @param string $cyno
     * @return EXcel
     */
    public function getExportData($id, $cyno)
    {
        $inventoried = [];
        $check = $this->inventoryRepository->checkInventoryUser($id, $cyno);
        if ($check) $inventoried = $this->inventoryRepository->exportData($cyno);
        return $inventoried;
    }
}

