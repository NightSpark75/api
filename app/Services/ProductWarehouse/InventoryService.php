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
    public function __construct(
        InventoryRepository $inventoryRepository
    ) {
        $this->inventoryRepository = $inventoryRepository;
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
        $nextItem = $this->inventoryRepository->getInventoryItem($cyno);
        return $nextItem;
    }
}