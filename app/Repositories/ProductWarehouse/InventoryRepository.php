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

    public function getCurrent($user)
    {
        $current = DB::selectOne("
            select a.pjcyno
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a, mpm_inventory_m m
                where a.pjcyno = m.cyno
                    and m.duser = '$user' and m.state = 'Y'
        ");
        return $current;
    }

    public function checkStartInventory($cyno)
    {
        $check = DB::selectOne("
            select count(*) n
                from mpm_inventory_m
                where cyno = '$cyno'
                    and state in ('Y', 'E')
        ")->n;
        return (int) $check === 0;
    }

    public function checkInventory($cyno, $user)
    {
        $check = DB::selectOne("
            select count(cyno) n
                from mpm_inventory_m
                where cyno = '$cyno' and duser = '$user'
                    and state = 'Y' 
        ")->n;
        return (int) $check > 0;
    }

    public function startInventory($cyno, $user)
    {
        DB::insert("
            insert into mpm_inventory_m
                values ('$cyno', '$user', sysdate, null, 'Y', sysdate)
        ");
        return true;
    }
    
    public function pauseInventory($cyno, $user)
    {
        DB::update("
            update mpm_inventory_m
                set state = 'P'
                where cyno = '$cyno' and duser = '$user' and state = 'Y'
        ");
        return true;
    }

    public function endInventory($cyno, $user)
    {
        DB::update("
            update mpm_inventory_m
                set state = 'E'
                where cyno = '$cyno' and duser = '$user' and state = 'Y'
        ");
        return true;
    }

    /**
     * get inventory list
     * 
     * @param string $date
     * @return array
     */
    public function getInventoryList($user, $date)
    {
        $list = DB::select("
            select a.pjcyno cyno
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a 
                where to_char(to_date(substr(a.pjcsdj,2,5),'YYDDD'),'YYYYMMDD') = '$date'
                    and (exists (
                        select * from mpm_inventory_m m 
                            where a.pjcyno = m.cyno
                                and ((duser = '$user' and state in ('Y')))
                    ) or (
                        select count(*) from mpm_inventory_m m
                            where a.pjcyno = m.cyno
                                and ((duser <> '$user' and state = 'Y') or state = 'E')
                    ) = 0)
                group by pjcyno
                    having count(pjcyno) <> (
                      select count(cyno)
                        from mpm_inventory_d b
                        where a.pjcyno = b.cyno
                    )
        ");
        return $list;
    }

    /**
     * get inventory item
     * 
     * @param string $cyno
     * @return array
     */
    public function getInventoryItem($cyno)
    {
        $item = DB::selectOne("
            select  a.pjcyno cyno, --盤點號碼
                    a.pjcsdj csdj, --盤點日期
                    trim(a.pjlocn) locn, --儲位
                    trim(a.pjlitm) litm, --料號
                    trim(a.pjlotn) lotn, --批號
                    a.pjtqoh tqoh, --庫存量
                    a.pjuom1 uom1,  --庫存單位
                    a.pjtqoh amount
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a
                where pjcyno = '$cyno' 
                    and not exists (
                        select cyno
                            from mpm_inventory_d i
                            where i.cyno = '$cyno'
                                and trim(a.pjlocn )= i.locn and trim(a.pjlitm) = i.litm and trim(a.pjlotn) = i.lotn
                    )
                order by pjlocn, pjlitm, pjlotn
        ");
        return $item;
    }

    /**
     * check inventory is finished or not
     * 
     * @param string $cyno
     * @return bool
     */
    public function checkFinished($cyno)
    {
        $items = DB::selectOne("
            select sum(case when locn is null then 1 else 0 end) items 
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a, mpm_inventory_d b
                where a.pjcyno = '$cyno'
                    and trim(a.pjcyno) = b.cyno(+) and trim(a.pjlocn) = b.locn(+) 
                    and trim(a.pjlitm) = b.litm(+) and trim(a.pjlotn) = b.lotn(+)
        ")->items;
        return (int) $items === 0;
    }

    /**
     * save inventory data
     * 
     * @param string $id
     * @param string $cyno
     * @param string $locn
     * @param string @litm
     * @param string $lotn
     * @param int $amount
     * @return bool
     */
    public function saveInventory($id, $cyno, $locn, $litm, $lotn, $amount)
    {
        DB::insert("
            insert into mpm_inventory_d 
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

    /**
     * check user's auth
     * 
     * @param string $id
     * @param string $cyno
     * @return bool
     */
    public function checkInventoryUser($id, $cyno)
    {
        $check = DB::select("
            select *
                from mpm_inventory_d
                where cyno = '$cyno' and duser = '$id'
        ");
        return count($check) > 0;
    }

    /**
     * return inventoried list
     * 
     * @param string $cyno
     * @return array
     */
    public function inventoried($cyno) 
    {
        $inventoried = DB::select("
            select amount, locn, litm, lotn
                    , stdadm.pk_hra.fu_emp_name(duser) duser
                    , to_char(ddate, 'YYYYMMDD HH24:MI:SS') ddate
                from mpm_inventory_d
                where cyno = '$cyno'
                order by locn, litm, lotn
        ");
        return $inventoried;
    }

    /**
     * inventoried export data
     * 
     * @param string $cyno
     * @return array
     */
    public function exportData($cyno)
    {
        $header = [['盤點數量', '儲位', '料號', '批號', '盤點人員', '時間']];
        $content = [];
        $list = $this->inventoried($cyno);
        for ($i = 0; $i < count($list); $i++) {
            array_push($content, [
                $list[$i]->amount,
                '\''.(string) $list[$i]->locn,
                '\''.(string) $list[$i]->litm,
                '\''.(string) $list[$i]->lotn,
                '\''.(string) $list[$i]->duser,
                '\''.(string) $list[$i]->ddate,
            ]);
        }
        $inventoried = array_collapse([$header, $content]);
        return $inventoried;
    }
}