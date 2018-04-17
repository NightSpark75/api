<?php
/**
 * picking repository
 *
 * @version 1.0.2
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/03/20
 * @since 1.0.0 spark: build database I/O function 
 * @since 1.0.1 spark: modify file name
 * @since 1.0.2 spark: completed unit test and optimized code
 * 
 */
namespace App\Repositories\ProductWarehouse;

use App\Repositories\Repository;

/**
 * Class PickingListRepository
 *
 * @package App\Repositories
 */
class PickingListRepository extends Repository
{   
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\Models\ProductWarehouse\PickingList';
    }

    /**
     * get picking list by date
     * 
     * @param string $date => 'Y-m-d 00:00:00'
     * @return mixed
     */
    public function getPickingList($date)
    {
        $list = $this->model
            ->where('staddj', $date)
            ->where('stky6', null)
            ->select('sticu', 'ststop', 'staddj', 'stky2', 'stky1')
            ->orderBy('stky1')
            ->orderBy('ststop')
            ->get();
        return $list;
    }

    /**
     * get picking by stop date
     * 
     * @param string $stop
     * @param string $date => 'Y-m-d 00:00:00'
     * @return mixed
     */
    public function getPicking($stop, $date)
    {
        $stop = str_pad($stop, 3, " ", STR_PAD_RIGHT);
        $picking = $this->model
            ->where('ststop', $stop)
            ->where('staddj', $date)
            ->first();
        return $picking;
    }

    /**
     * call procedure proc_upd_f594921_pick_s
     * 
     * @param string $stop
     * @param string $date // ex: 2018/01/05
     * @param string $user
     * @return mixed
     */
    public function startPicking($stop, $date, $user) 
    {
        $procedure = 'proc_upd_f594921_pick_s';
        $parameters = [
            ':stop' => $stop,
            ':date' => $date,
            ':user' => $user,
        ];
        $this->procedure($procedure, $parameters);
        return true;
    }

    /**
     * call procedure proc_upd_f594921_pick_e
     * 
     * @param string $stop
     * @param string $date // ex: 2018/01/05
     * @param string $user
     */
    public function endPicking($stop, $date, $user) 
    {
        $procedure = 'proc_upd_f594921_pick_e';
        $parameters = [
            ':stop' => $stop,
            ':date' => $date,
            ':user' => $user,
        ];
        $this->procedure($procedure, $parameters);
        return true;
    }

    /**
     * call procedure
     * 
     * @param string $stop
     * @param string $date
     * @param string $user
     */
    public function pausePicking($stop, $date, $user)
    {
        return true;
    }

    /**
     * call procedure
     * 
     * @param string $stop
     * @param string $date
     * @param string $user
     */
    public function restartPicking($stop, $date, $user)
    {
        return true;
    }
}