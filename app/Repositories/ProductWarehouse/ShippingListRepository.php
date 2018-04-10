<?php
/**
 * shipping repository
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/01/22
 * @since 1.0.0 spark: build database I/O function 
 * @since 1.0.1 spark: file name
 * 
 */
namespace App\Repositories\ProductWarehouse;

use App\Repositories\Repository;

/**
 * Class ShippingListRepository
 *
 * @package App\Repositories
 */
class ShippingListRepository extends Repository
{   
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\Models\ProductWarehouse\ShippingList';
    }

    /**
     * get shipping info by spno and date
     * 
     * @param string $date => 'Y-m-d 00:00:00'
     * @return mixed
     */
    public function getShippingInfo($spno)
    {
        $info = $this->model
            ->where('tmy59spno', $spno)
            ->where('tmaddj', '1899-12-31 00:00:00')
            ->select('tmtrdj', 'tmaddj', 'tmy59spno', 'tmcars', 'cars_na', 'tman8', 'tmalph', 'tm1in1', 'dltm_na', 'tmalph1')
            ->first();
        return $info;
    }

    /**
     * check shipping info by spno and tmtrdj
     */

    public function checkShippingInfo($spno, $tmtrdj)
    {
        $info = $this->model
            ->where('tmy59spno', $spno)
            ->where('tmtrdj', $tmtrdj)
            //->where('tmaddj', null)
            ->get();
        return count($info) > 0;
    }

    /**
     * call proc_upd_f594901_1in1 procedure
     * 
     * @param string $spno
     * @param string $date
     * @param string $user 
     * @param string $pieces
     * @return bool
     */
    public function savePieces($spno, $date, $user, $pieces)
    {
        
        $procedure = 'proc_upd_f594901_1in1';
        $parameters = [
            ':spno' => $spno,
            ':date' => $date,
            ':pieces' => $pieces,
        ];
        $this->procedure($procedure, $parameters);
        return true;
    }
}