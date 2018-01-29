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

use App\Repositories\ProductWarehouse\PickingListRepository;
use App\Repositories\ProductWarehouse\PickingItemsRepository;
use Exception;
use DB;

/**
 * Class PickingService
 *
 * @package App\Services
 */
class PickingService {

    /**
     * @var PickingListRepository
     */
    private $pickingListRepository;
    
    /**
     * @var PickingItemsRepository
     */
    private $pickingItemsRepository;

    /**
     * @param PickingListRepository $pickingListRepository
     * @param PickingItemRepository $pickingItemsRepository
     */
    public function __construct(
        PickingListRepository $pickingListRepository,
        PickingItemsRepository $pickingItemsRepository
    ) {
        $this->pickingListRepository = $pickingListRepository;
        $this->pickingItemsRepository = $pickingItemsRepository;
    }

    /**
     * call procedure proc_upd_f594921
     * 
     * @param string $stop
     * @param string $date // ex: 2018/01/05
     * @param string $empno
     * @param string $ky3
     * @param string $ky6
     * @param string $ky7
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
        return true;
    }

    /**
     * get today picking list
     *
     * @return mixed
     */
    public function getTodayPickingList($today = null)
    {
        $date = $today? $today: date('Y-m-d').' 00:00:00';
        $list = $this->pickingListRepository->getPickingList($date);
        return $list;
    }

    /**
     * post start picking update start time
     *
     * @param string $stop
     * @param string $empno
     */
    public function startPicking($stop, $empno, $today = null, $ky3 = null)
    {
        $date = $today? $today: date('Y-m-d').' 00:00:00';
        $picking = $this->pickingListRepository->getPicking($stop, $date);
        
        if ($picking) {
            $staddj = date_format(date_create($date), 'Y/m/d');
            $stky3 = $ky3? $ky3: date('H:i:s');
            $stky6 = $picking->stky6;
            $stky7 = $picking->stky7;
            $this->procUpdF594921($stop, $staddj, $empno, $stky3, $stky6, $stky7);
            return true;
        }
        throw new Exception("ststop='$stop' and staddj='$date', data not found!");
    }

    /**
     * post end picking and update end time and status
     *
     * @param string $stop
     * @param string $empno
     */
    public function endPicking($stop, $empno, $today = null, $ky6 = null, $ky7 = null)
    {
        $date = $today? $today: date('Y-m-d').' 00:00:00';
        $picking = $this->pickingListRepository->getPicking($stop, $date);
        if ($picking) {
            $staddj = date_format(date_create($date), 'Y/m/d');
            $ky3 = $picking->stky3;
            $stky6 = $ky6? $ky6: 'Y';
            $stky7 = $ky7? $ky7: date('H:i:s');
            $this->procUpdF594921($stop, $staddj, $empno, $ky3, $stky6, $stky7);
            return true;
        }
        throw new Exception("ststop='$stop' and staddj='$date', data not found!");
    }
}