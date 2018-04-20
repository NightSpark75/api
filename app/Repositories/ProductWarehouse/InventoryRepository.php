<?php
/**
 * inventory repository
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/04/19
 * @since 1.0.0 spark: build database I/O function 
 * @since 1.0.1 spark: file name
 * 
 */
namespace App\Repositories\ProductWarehouse;

use DB;
use App\Repositories\Repository;

/**
 * Class InventoryRepository
 *
 * @package App\Repositories
 */
class InventoryRepository extends Repository
{   
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\Models\ProductWarehouse\Inventory';
    }

    public function getInventoryList($date)
    {
        $list = DB::select("
            select unique a.pjcyno cyno
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a
                where to_char(to_date(substr(a.pjcsdj,2,5),'YYDDD'),'YYYYMMDD') = '$date'
        ");
        return $list;
    }

    public function getInventoryItem($cyno)
    {
        $item = DB::selectOne("
        select  a.pjcyno cyno, --盤點號碼
                a.pjcsdj csdj, --盤點日期
                trim(a.pjlocn) locn, --儲位
                trim(a.pjlitm) litm, --料號
                trim(a.pjlotn) lotn, --批號
                a.pjtqoh tqoh, --庫存量
                a.pjuom1 uom1  --庫存單位
            from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a
            where pjcyno = '$cyno' 
                and not exists (
                    select cyno
                        from mpm_inventory i
                        where i.cyno = '$cyno'
                            and trim(a.pjlocn )= i.locn and trim(a.pjlitm) = i.litm and trim(a.pjlotn) = i.lotn
                )
            order by pjlocn, pjlitm, pjlotn
        ");
        return $item;
    }

    public function saveInventory($id, $cyno, $locn, $litm, $lotn, $amount)
    {
        DB::insert("
            insert into mpm_inventory 
                values (:cyno, :locn, :litm, :lotn, :amount, :id, sysdate)
        ", [
            'cyno' => $cyno, 
            'locn' => $locn,
            'litm' => $litm,
            'lotn' => $lotn,
            'amount' => (int) $amount,
            'id' => $id,
        ]);
        return true;
    }

    public function checkInventoryUser($id, $cyno)
    {
        $check = DB::select("
            select *
                from mpm_inventory
                where cyno = '$cyno' and duser = '$id'
        ");
        return $check > 0;
    }

    public function inventoried($cyno) 
    {
        $inventoried = DB::select("
            select *
                from mpm_inventory
                where cyno = '$cyno'
        ");
        return $inventoried;
    }

    public function export($cyno)
    {
        // $header = [[
        //     mb_convert_encoding('盤點數量', "utf-8", "big5"), 
        //     mb_convert_encoding('儲位', "utf-8", "big5"), 
        //     mb_convert_encoding('料號', "utf-8", "big5"), 
        //     mb_convert_encoding('批號', "utf-8", "big5"),
        // ]];
        $header = [['盤點數量', '儲位', '料號', '批號']];
        $inventory = $this->model
            ->where('cyno', $cyno)
            ->select('amount', 'locn', 'litm', 'lotn')
            ->orderBy('locn')
            ->orderBy('litm')
            ->orderBy('lotn')
            ->get()
            ->toArray();
        $inventory = array_collapse([$header, $inventory]);
        return $inventory;
    }
}