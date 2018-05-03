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
use DB;

/**
 * Class PickingListRepository
 *
 * @package App\Repositories
 */
class PickingListRepository extends Repository
{   
    private $jdv_f594921 = 'jdv_f594921';
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\Models\ProductWarehouse\PickingList';
    }

    public function getCurrent($user, $date)
    {
        $current = DB::selectOne("
            select j.sticu, trim(j.ststop) ststop, j.staddj
                from $jdv_f594921 j, mpm_picking_m m
                where trim(j.ststop) = m.stop and j.staddj = to_date(m.addj, 'YYYYMMDD')
                    and m.duser = '$user' and m.addj = $date
                    and m.state = 'Y'
        ");
        return $current;
    }

    /**
     * get picking list by date
     * 
     * @param string $date => 'Y-m-d 00:00:00'
     * @return mixed
     */
    public function getPickingList($user, $date)
    {
        $list = DB::select("
            select j.sticu, trim(j.ststop) ststop, j.staddj
                from $jdv_f594921 j
                where j.staddj = to_date($date, 'YYYYMMDD') and j.stky6 is null
                    and (exists (
                        select * from mpm_picking_m m 
                            where j.staddj = to_date(m.addj, 'YYYYMMDD')
                                and trim(ststop) = m.stop
                                and ((duser = '106013' and state in ('Y')))
                    ) or (
                        select count(*) from mpm_picking_m m
                            where j.staddj = to_date(m.addj, 'YYYYMMDD')
                                and trim(ststop) = m.stop
                                and ((duser <> '106013' and state = 'Y') or state = 'E')
                    ) = 0)
        ");
        return $list;
    }

    public function checkStartPicking($stop, $date)
    {
        $check = DB::selectOne("
            select count(*) n
                from mpm_picking_m
                where stop = '$stop'
                    and addj = '$date'
                    and state in ('Y', 'E')
        ")->n;
        return (int) $check === 0;
    }

    /**
     * check picking by stop, date and user
     * 
     * @param string $stop
     * @param string $date => 'Y-m-d 00:00:00'
     * @return mixed
     */
    public function checkPicking($stop, $date, $user)
    {
        $check = DB::selectOne("
            select count(j.ststop) n
                from $jdv_f594921 j, mpm_picking_m m
                where trim(j.ststop) = m.stop
                    and j.staddj = to_date(m.addj, 'YYYYMMDD')
                    and j.stky6 is null
                    and m.stop = '$stop'
                    and m.addj = $date
                    and m.duser = '$user'
                    and m.state = 'Y'
        ")->n;
        return (int) $check > 0;
    }

    /**
     * call procedure proc_start_picking
     * 
     * @param string $stop
     * @param string $date // ex: 2018/01/05
     * @param string $user
     * @return mixed
     */
    public function startPicking($stop, $date, $user) 
    {
        $procedure = 'pk_mpm.proc_start_picking';
        $parameters = [
            ':stop' => $stop,
            ':date' => $date,
            ':user' => $user,
        ];
        $this->procedure($procedure, $parameters);
        return true;
    }

    /**
     * call procedure proc_end_picking 
     * 
     * @param string $stop
     * @param string $date // ex: 2018/01/05
     * @param string $user
     */
    public function endPicking($stop, $date, $user) 
    {
        $procedure = 'pk_mpm.proc_end_picking';
        $parameters = [
            ':stop' => $stop,
            ':date' => $date,
            ':user' => $user,
        ];
        $this->procedure($procedure, $parameters);
        return true;
    }

    /**
     * call procedure proc_pause_picking
     * 
     * @param string $stop
     * @param string $date
     * @param string $user
     */
    public function pausePicking($stop, $date, $user)
    {
        $procedure = 'pk_mpm.proc_pause_picking';
        $parameters = [
            ':stop' => $stop,
            ':date' => $date,
            ':user' => $user,
        ];
        $this->procedure($procedure, $parameters);
        return true;
    }
}