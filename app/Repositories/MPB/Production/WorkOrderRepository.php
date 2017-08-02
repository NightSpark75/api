<?php
/**
 * 生產派工流程資料
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/08/01
 * @since 1.0.0 spark: 建立檔案寫入與讀取相關的資料處理
 * 
 */
namespace App\Repositories\MPB\Production;

use DB;
use Exception;
use Auth;

/**
 * Class WorkOrderRepository
 *
 * @package App\Repositories\MPB\Production
 */
class WorkOrderRepository
{   
    public function __construct() {

    }

    public function init()
    {
        try{
            $user_id = auth()->user()->id;
            $where = $this->getOrderWhere($user_id);
            $order_d = DB::select("
                select *
                from mpb_order_d
                where $where
            ");
            $result = [
                'result' => true,
                'msg' => '取得生產清單成功!(#0001)',
                'order_d' => $order_d,
            ];
            return $result;
        } catch (Exception $e) {
            $result = [
                'result' => false,
                'msg' => $e->getMessage(),
            ];
            return $result;
        }
    }

    private function getOrderWhere($user_id) {
        $type = DB::selectOne("
            select count(*) as count
            from mpa_dept_emp 
            where empno = '$user_id' 
        ");
        if ($type->count === 0) {
            $where = DB::selectOne("
                select pk_mpb.fu_order_where('1', '%', '$user_id') as str
                from dual
            ");
        } else {
            $where = DB::selectOne("
                select pk_mpb.fu_order_where('2', '', '$user_id') as str
                from dual
            ");
        }
        return $where->str;
    }
}   