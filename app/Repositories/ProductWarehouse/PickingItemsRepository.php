<?php
/**
 * picking items repository
 *
 * @version 1.0.2
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/03/20
 * @since 1.0.0 spark: build database I/O function 
 * @since 1.0.2 spark: completed unit test and optimized code
 * 
 */
namespace App\Repositories\ProductWarehouse;

use App\Repositories\Repository;
use DB;

/**
 * Class PickingItemsRepository
 *
 * @package App\Repositories
 */
class PickingItemsRepository extends Repository
{       
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\Models\ProductWarehouse\PickingItems';
    }

    /**
     * get picking items by date and stop
     * 
     * @param string $stop 
     * @param string $date => 'Y-m-d 00:00:00'
     * @return mixed
     */
    public function getPickingItem($stop, $date, $user)
    {   
        $list = DB::selectOne("
            select j.psicu, j.psaddj, 
                    trim(j.psstop) psstop, trim(j.pslocn) pslocn, trim(j.psrmk) psrmk, trim(j.pslitm) pslitm, trim(j.pslotn) pslotn, 
                    j.pssoqs, j.pspqoh, j.psuom
                from jdv_f5942520 j, mpm_picking_d d
                where psstop = '$stop' and psaddj = to_date($date, 'YYYYMMDD')
                    and (
                        trim(j.psstop) = d.stop and d.addj = $date
                        and trim(j.psrmk) <> d.rmk
                        and trim(j.pslitm) <> d.litm
                        and trim(j.pslotn) <> d.lotn
                    )
                order by j.psseq
        ");
        return $list;
    }

    /**
     * call procedure proc_pickup
     * 
     * @param string $stop
     * @param string $date
     * @param string $rmk
     * @param string $litm
     * @param string $lotn
     * @param string $user
     * @return void
     */
    public function pickup($stop, $date, $rmk, $litm, $lotn, $user)
    {
        $procedure = 'proc_pickup';
        $parameters = [
            ':stop' => $stop,
            ':date' => $date,
            ':rmk'  => $rmk,
            ':litm' => $litm,
            ':lotn' => $lotn,
            ':user' => $user,
        ];
        $this->procedure($procedure, $parameters);
        return true;
    }
}