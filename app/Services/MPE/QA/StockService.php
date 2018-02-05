<?php
/**
 * qa stock service
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/02/05
 * @since 1.0.0 spark: handle stock business logic
 * 
 */
namespace App\Services\MPE\QA;

use App\Repositories\MPE\QA\StockRepository;
use Exception;
use DB;

/**
 * Class StockService
 *
 * @package App\Services
 */
class StockService {

    /**
     * @var StockRepository
     */
    private $stockRepository;

    /**
     * @param StockRepository $StockRepository
     */
    public function __construct(
        StockRepository $stockRepository
    ) {
        $this->stockRepository = $stockRepository;
    }

    /**
     * get item info by barcode
     *
     * @param string $barcode
     * @return mixed
     */
    public function getItemInfo($barcode)
    {
        $info = $this->stockRepository->getItemByBarcode($barcode);
        return $info;
    }
}