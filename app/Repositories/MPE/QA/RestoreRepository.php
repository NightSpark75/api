<?php
/**
 * QA回庫資料處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/10/20
 * @since 1.0.0 spark: 建立檔案寫入與讀取相關的資料處理
 * 
 */
namespace App\Repositories\MPE\QA;

use DB;
use Exception;
use Auth;
use App\Traits\Sqlexecute;

/**
 * Class ReceiveRepository
 *
 * @package App\Repositories\MPE\QA
 */
class RestoreRepository
{   
    use Sqlexecute;
    
    public function __construct() 
    {

    }

    public function getReceiveList() {
        $restore = DB::select("
            select he.barcode, he.partno, he.batch, he.whouse, he.stor, he.grid
                , pk_mpe.fu_pname(he.partno) pname
                , pk_mpe.fu_posit(he.whouse) posit
                , pk_mpe.fu_storn(he.whouse, he.stor) storn
                , pk_mpe.fu_get_usize(he.partno, he.batch, he.whouse, he.stor, he.grid) usize
                , pk_mpe.fu_get_receive_qty(he.barcode) receive_qty
                , pk_mpe.fu_get_all_receive_qty(he.barcode) all_receive_qty
                , pk_mpe.fu_get_apply_qty(he.barcode) apply_qty
                , pk_mpe.fu_get_stor_me(he.partno) stor_me
                , pk_mpe.fu_get_unit(he.partno, he.batch, he.whouse, he.stor, he.grid) unit
                , pk_mpe.fu_get_mcu(he.partno, he.batch, he.whouse, he.stor, he.grid) mcu
                , pk_mpe.fu_get_ldate(he.partno, he.batch, he.whouse, he.stor, he.grid) ldate
            from mpe_house_e he
            where he.code = '04' and he.sta = 'Y'
                and he.barcode in (
                    select barcode
                        from mpe_lsa_e
                        where status = 'R'
                )
        ");

        return $this->success(['restore' => $restore]);
    }

    public function restoreSave($barcode, $amt)
    {
        try {
            DB::transaction( function () use($barcode, $amt) {
                $user = auth()->user()->id;
                $today = date('Ymd');

                DB::update("
                    update mpe_house_e
                        set sta = 'N', amt = :amt
                        where barcode = :barcode
                ", ['barcode' => $barcode, 'amt' => $amt]);

                DB::update("
                    update mpe_lsa_e
                        set status = 'C'
                        where barcode = :barcode and status = 'R'
                ", ['barcode' => $barcode]);

                $rec_no = DB::selectOne("select pk_mpe.fu_create_no('PI') rec_no from dual")->rec_no;
                $dept_no = DB::selectOne("select deptno from stdadm.v_hra_emp_dept1 where empno = '$user'")->deptno;

                DB::insert("
                    insert into mpe_rec_m
                        (code, sinnum, typ, stat, rmk, ouser, odept, odate, post, duser, ddate, ldate)
                    values 
                        ('04', '$rec_no', 'A', '', '回庫', '$user', '$dept_no', to_number(to_char(sysdate, 'YYYYMMDD'))
                            ,'Y', '$user', sysdate, to_number(to_char(sysdate, 'YYYYMMDD')))
                ");
                
                DB::insert("
                    insert into mpe_rec_d
                        (code, sinnum, barcode, partno, whouse, stor, grid, batch, rmk, duser, ddate, usize)
                    select '04', '$rec_no', e.barcode, e.partno, e.whouse, e.stor, e.grid, e.batch,  '回庫:'||:amt, '$user', sysdate, :amt
                        from mpe_house_e e
                        where e.barcode = :barcode
                ", ['barcode' => $barcode, 'amt' => $amt]);
            });
            DB::commit();
            return $this->success(['msg' => '回庫過帳成功!(#0003)']);
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }
}