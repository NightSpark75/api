<?php
/**
 * point service
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/04/18
 * @since 1.0.0 spark: handle point 
 * 
 */
namespace App\Services\MPZ;

use App\Repositories\MPZ\PointlogRepository;
use App\Services\CommonService;
use Exception;
use DB;

/**
 * Class PointService
 *
 * @package App\Services
 */
class PointService {
    /**
     * @var PointlogRepository
     */
    private $pointlogRepository;

    private $common;

    /**
     * @param PointlogRepository $pointlogRepository
     * @param CommonService $common
     */
    public function __construct(
        PointlogRepository $pointlogRepository,
        CommonService $common
    ) {
        $this->pointlogRepository = $pointlogRepository;
        $this->common = $common;
    }

    public function init()
    {

    }
}