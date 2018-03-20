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

use DB;
use Exception;
use App\Traits\Sqlexecute;
use App\Traits\Oracle;
use App\Repositories\Repository;

/**
 * Class ShippingListRepository
 *
 * @package App\Repositories
 */
class ShippingListRepository extends Repository
{   
    use Sqlexecute;
    use Oracle;

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
            ->where('staddj', $date)
            ->where('stky6', null)
            //->where('stky2', null)
            //->where('stky5', null)
            ->select('sticu', 'ststop', 'staddj', 'stky2')
            ->orderBy('ststop')
            ->get();
        return $list;
    }

    /**
     * get shipping by stop date
     * 
     * @param string $stop
     * @param string $date => 'Y-m-d 00:00:00'
     * @return mixed
     */
    public function getShipping($stop, $date)
    {
        $stop = str_pad($stop, 3, " ", STR_PAD_RIGHT);
        $shipping = $this->model
            ->where('ststop', $stop)
            ->where('staddj', $date)
            ->first();
        return $shipping;
    }

    /**
     * call procedure proc_upd_f594921_pick_s
     * 
     * @param string $stop
     * @param string $date // ex: 2018/01/05
     * @param string $user
     */
    public function startShipping($stop, $date, $user) 
    {
        $pdo = DB::getPdo();
        $stmt = $pdo->prepare("begin proc_upd_f594921_pick_s(:stop, :date, :user); end;");
        $stmt->bindParam(':stop', $stop);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':user', $user);
        $stmt->execute();
    }

    /**
     * call procedure proc_upd_f594921_pick_e
     * 
     * @param string $stop
     * @param string $date // ex: 2018/01/05
     * @param string $user
     */
    public function endShipping($stop, $date, $user) 
    {
        $pdo = DB::getPdo();
        $stmt = $pdo->prepare("begin proc_upd_f594921_pick_e(:stop, :date, :user); end;");
        $stmt->bindParam(':stop', $stop);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':user', $user);
        $stmt->execute();
    }

    /**
     * call procedure proc_upd_f594921_ship_s
     * 
     * @param string $stop
     * @param string $date // ex: 2018/01/05
     * @param string $user
     */
    public function startShiping($stop, $date, $user) 
    {
        $pdo = DB::getPdo();
        $stmt = $pdo->prepare("begin proc_upd_f594921_ship_s(:stop, :date, :user); end;");
        $stmt->bindParam(':stop', $stop);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':user', $user);
        $stmt->execute();
    }

    /**
     * call procedure proc_upd_f594921_ship_e
     * 
     * @param string $stop
     * @param string $date // ex: 2018/01/05
     * @param string $user
     */
    public function endShiping($stop, $date, $user) 
    {
        $pdo = DB::getPdo();
        $stmt = $pdo->prepare("begin proc_upd_f594921_ship_e(:stop, :date, :user); end;");;
        $stmt->bindParam(':stop', $stop);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':user', $user);
        $stmt->execute();
    }
}