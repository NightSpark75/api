<?php
/**
 * picking service
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/01/25
 * @since 1.0.0 spark: handle picking business logic
 * 
 */
namespace App\Services\ProductWarehouse;

use App\Repositories\ProductWarehouse\PickingRepository;
use App\Repositories\ProductWarehouse\PickingItemsRepository;
use JWTAuth;
use DB;

/**
 * Class PickingService
 *
 * @package App\Services
 */
class PickingService {

    /**
     * @var PickingRepository
     * @var PickingItemsRepository
     */
    private $pickingRepository;
    private $pickingItemsRepository;

    /**
     * @param PickingRepository $pickingRepository
     * @param PickingItemRepository $pickingItemsRepository
     * @throws Exception
     */
    public function __construct(
        PickingRepository $pickingRepository,
        PickingItemsRepository $pickingItemsRepository
    ) {
        $this->pickingRepository = $pickingRepository;
        $this->pickingItemsRepository = $pickingItemsRepository;
    }

    /**
     * call procedure proc_upd_f594921
     * 
     * @param string $stop
     * @param string $date
     * @param string $empno
     * @param string $ky3
     * @param string $ky6
     * @param string $ky7
     * @return mixed
     */
    private function procUpdF594921($stop, $date, $empno, $ky3, $ky6, $ky7)
    {
        $pdo = DB::getPdo();
        $stmt = $pdo->prepare("begin proc_upd_f594921(:stop, :date, :empno, :ky3, :ky6, :ky7); end;");;
        $stmt->bindParam(':stop', $stop);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':empno', $empno);
        $stmt->bindParam(':ky3', $ky3);
        $stmt->bindParam(':ky6', $ky6);
        $stmt->bindParam(':ky7', $ky7);
        $stmt->execute();
    }

    /**
     * get today picking items
     *
     * @param string $stop
     * @return mixed
     */
    public function getPickingItems($stop)
    {
        $today = '2018-01-24 00:00:00';
        //$today = date('Y-m-d').' 00:00:00';
        $items = $this->pickingRepository->getPickingList($today);
        return $items;
    }

    /**
     * get today picking list
     *
     * @return mixed
     */
    public function getTodayPickingList()
    {
        $today = '2018-01-25 00:00:00';
        //$today = date('Y-m-d').' 00:00:00';
        $list = $this->pickingRepository->getPickingList($today);
        return $list;
    }

    /**
     * post start picking update start time
     *
     * @param string $stop
     * @param string $empno
     * @throws Exception
     * @return mixed
     */
    public function startPicking($stop, $empno)
    {
        $today = '2018-01-25 00:00:00';
        $date = '2018/01/25';
        //$today = date('Y-m-d').' 00:00:00';
        $picking = $this->pickingRepository->getPicking($stop, $today);
        $ky3 = date('H:i:s');
        $ky6 = $picking->stky6;
        $ky7 = $picking->stky7;
        $this->procUpdF594921($stop, $date, $empno, $ky3, $ky6, $ky7);
        return null;
    }

    /**
     * post end picking and update end time and status
     *
     * @param string $stop
     * @param string $empno
     * @throws Exception
     * @return mixed
     */
    public function endPicking($stop, $empno)
    {
        $today = '2018-01-25 00:00:00';
        $date = '2018/01/25';
        //$today = date('Y-m-d').' 00:00:00';
        $picking = $this->pickingRepository->getPicking($stop, $today);
        $ky3 = $picking->stky3;
        $ky6 = 'Y';
        $ky7 = date('H:i:s');
        $this->procUpdF594921($stop, $date, $empno, $ky3, $ky6, $ky7);
        return null;
    }
}