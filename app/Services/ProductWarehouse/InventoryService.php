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
     * @var InventoryListRepository
     */
    private $inventoryRepository;

    /**
     * @param InventoryListRepository $inventoryRepository
     */
    public function __construct(
        InventoryListRepository $inventoryRepository
    ) {
        $this->inventoryRepository = $inventoryRepository;
    }

    
}