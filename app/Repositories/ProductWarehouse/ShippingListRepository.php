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
     * get shipping list by date
     * 
     * @param string $date => 'Y-m-d 00:00:00'
     * @return mixed
     */
    public function getShippingList($date)
    {
        $list = $this->model
            ->where('tmtrdj', $date)
            ->select('tmurab', 'tmaddj', 'tmy59spno', 'tmcars', 'tman8')
            ->orderBy('tmy59spno')
            ->get();
        return $list;
    }

    /**
     * call procedure proc_upd_f594921_ship_s
     * 
     * @param string $stop
     * @param string $date // ex: 2018/01/05
     * @param string $user
     */
    public function startShiping($spno, $date, $user) 
    {
        /*
        $pdo = DB::getPdo();
        $stmt = $pdo->prepare("begin proc_upd_f594921_ship_s(:stop, :date, :user); end;");
        $stmt->bindParam(':stop', $stop);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':user', $user);
        $stmt->execute();
        */
    }

    /**
     * call procedure proc_upd_f594921_ship_e
     * 
     * @param string $stop
     * @param string $date // ex: 2018/01/05
     * @param string $user
     */
    public function endShiping($spno, $date, $user) 
    {
        /*
        $pdo = DB::getPdo();
        $stmt = $pdo->prepare("begin proc_upd_f594921_ship_e(:stop, :date, :user); end;");;
        $stmt->bindParam(':stop', $stop);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':user', $user);
        $stmt->execute();
        */
    }
}