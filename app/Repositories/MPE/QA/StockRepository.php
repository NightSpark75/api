<?php
/**
 * QA庫存資料處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/07/24
 * @since 1.0.0 spark: 建立檔案寫入與讀取相關的資料處理
 * 
 */
namespace App\Repositories\MPE\QA;

use DB;
use Exception;
use Auth;
use App\Traits\Sqlexecute;

/**
 * Class StockRepository
 *
 * @package App\Repositories\MPE\QA
 */
class StockRepository
{   
    use Sqlexecute;
    
    public function __construct() {

    }

    public function getStockList($str = '') {
        try {
            $stock = DB::select("
                select m.partno, mm.pname, m.batch, m.whouse, m.stor
                from mpe_house_m m, mpe_mate mm
                where (m.partno like :str or m.batch like :str)
                    and m.code = '04'
                    and m.partno = mm.partno
                order by ldate desc, batch
            ", ['str' => '%'.$str.'%']);
            $storage = $str === '' ? null : $this->getStorageList();
            $result = [
                'result' => true,
                'msg' => '查詢庫存清單成功!(#0001)',
                'list' => $stock,
                'storage' => $storage,
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

    private function getStorageList() {
        try{
            $storage = DB::select("
                select m.whouse, m.posit, d.stor, d.storn
                from mpe_whs_m m, mpe_whs_d d
                where m.whouse = d.whouse and m.code = '04'
                order by m.whouse, d.stor desc
            ");
            return $storage;
        } catch (Excepation $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function storageChange($params) {
        try {
            DB::transaction( function () use($params) {  
                DB::update("
                    update mpe_house_e
                        set whouse = :whouse,
                            stor = :stor
                        where partno = :partno
                            and batch = :batch
                ", $params);

                DB::update("
                    update mpe_house_m
                        set whouse = :whouse,
                            stor = :stor
                        where partno = :partno
                            and batch = :batch
                ", $params);                
            });
            DB::commit();
            $result = [
                'result' => true,
                'msg' => '變更儲位成功!(#0002)'
            ];
            return $result;
        } catch (Exception $e) {
            DB::rollback();
            $result = [
                'result' => false,
                'msg' => $e->getMessage(),
            ];
            return $result;
        }
    }

    public function getItemByBarcode($barcode)
    {
        $info = DB::selectOne("
            select e.partno, pk_mpe.fu_pname(e.partno) pname, e.whouse, pk_mpe.fu_posit(e.whouse) posit, e.stor, pk_mpe.fu_storn(e.whouse, e.stor) storn
                    , e.batch, e.amt, m.unit, m.usize, e.rmk
                    , case when e.sta = 'N' then '在庫' when e.sta = 'Y' then '領用' else e.sta end sta 
                from mpe_house_e e, mpe_house_m m
                where e.code = '04' and e.barcode = '$barcode'
                    and m.partno = e.partno and m.batch = e.batch
                    and m.whouse = e.whouse and m.stor = e.stor and m.grid = e.grid
        ");
        return $info;
    }
}   