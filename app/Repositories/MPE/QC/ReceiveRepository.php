<?php
/**
 * QC領料資料處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/07/28
 * @since 1.0.0 spark: 建立檔案寫入與讀取相關的資料處理
 * 
 */
namespace App\Repositories\MPE\QC;

use DB;
use Exception;
use Auth;
use App\Traits\Sqlexecute;

/**
 * Class ReceiveRepository
 *
 * @package App\Repositories\MPE\QC
 */
class ReceiveRepository
{   
    use Sqlexecute;
    
    public function __construct() {

    }

    public function init()
    {
        try {
            $barcode = $this->getBarcode();
            $result = [
                'result' => true,
                'msg' => '取得條碼清單資料成功!(#0001)',
                'barcode' => $barcode,
            ];
            return $result;
        } catch (Exception $e) {
            return $this->exception($e);
        }
        
    }

    private function getBarcode()
    {
        $barcode = DB::select("
            select e.*, m.pname, m.molef, m.molew, m.casno, m.lev, m.conc, m.scrap, m.sfty, m.unit, 
                m.toxicizer, m.hazard, m.ename, m.reagent, m.pioneer, m.sds_no, m.sdsdate, m.ops, h.usize,
                (select sum(amt)
                    from mpe_house_e se
                    where e.code = se.code and e.partno = se.partno and e.batch = se.batch
                        and e.whouse = se.whouse and e.stor = se.stor and se.sta = 'N'
                    group by code, partno, batch, whouse, stor
                ) qty
            from mpe_house_e e, mpe_mate m, mpe_house_m h
            where e.code = '01' and e.code = m.code and e.partno = m.partno
                and e.code = h.code and e.partno = h.partno and e.batch = h.batch
                and e.whouse = h.whouse and e.stor = h.stor and e.sta = 'N'
        ");
        return $barcode;
    }

    public function posting($receive_list) 
    {
        
        try{
            $select = DB::select("
                select to_number(substr(max(no), 2, 11) + 1) no
                from mpe_receive_m
                where code = '01' and substr(no, 2, 8) = to_char(sysdate, 'YYYYMMDD')
            ");
            if (!isset($select[0]->no)) {
                $no = 'R'.date('Ymd').'001';
            } else {
                $no = 'R'.$select[0]->no;
            }

            DB::transaction( function () use($no, $receive_list) {
                $user = auth()->user()->id;
                $today = date('Ymd');

                $rec_no = DB::selectOne("select pk_mpe.fu_create_no('PI') rec_no from dual")->rec_no;
                $dept_no = DB::selectOne("select deptno from stdadm.v_hra_emp_dept1 where empno = '$user'")->deptno;
                
                DB::insert("
                    insert into mpe_rec_m
                        (code, sinnum, typ, ouser, odept, odate, post, duser, ddate, ldate)
                    values 
                        ('01', '$rec_no', 'A', '$user', '$dept_no', to_number(to_char(sysdate, 'YYYYMMDD'))
                            ,'Y', '$user', sysdate, to_number(to_char(sysdate, 'YYYYMMDD')))
                ");

                for ($i = 0; $i < count($receive_list); $i++) {
                    $item = $receive_list[$i];
                    $item->receive_no = $no;

                    $binds = [
                        'code' => $item->code,
                        'sinnum' => $rec_no,
                        'barcode' => $item->barcode,
                        'partno' => $item->partno,
                        'whouse' => $item->whouse,
                        'stor' => $item->stor,
                        'grid' => $item->grid,
                        'batch' => $item->batch,
                        'rmk' => $item->rmk,
                        'duser' => $user,
                        'amt' => $item->amt,
                    ];

                    DB::insert("
                        insert into mpe_rec_d
                            (code, sinnum, barcode, partno, whouse, stor, grid, batch, rmk, duser, ddate, usize)
                        values
                            (:code, :sinnum, :barcode, :partno, :whouse, :stor, :grid, :batch, :rmk, :duser, sysdate, :amt)
                    ", $binds);

                    DB::update("
                        update mpe_house_e e
                        set sta = 'Y'
                          , opdate = to_number(to_char(sysdate, 'YYYYMMDD'))
                          , opvl = (
                            select 
                            case 
                              when e.valid > (
                                case 
                                  when typ = 'A' then to_number(to_char(add_months(sysdate, 24)-1, 'YYYYMMDD'))
                                  when typ = 'B' then to_number(to_char(add_months(sysdate, 12)-1, 'YYYYMMDD'))
                                end) 
                                then (case when typ = 'A' then to_number(to_char(add_months(sysdate, 24)-1, 'YYYYMMDD'))
                                  when typ = 'B' then to_number(to_char(add_months(sysdate, 12)-1, 'YYYYMMDD')) end)
                              else e.valid 
                            end
                            from mpe_mate m
                            where e.partno = m.partno
                          )
                        where e.barcode = :barcode
                    ", ['barcode' => $item->barcode]);
                }
            });
            DB::commit();
            $result = [
                'result' => true,
                'msg' => '領用過帳成功!(#0002)',
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
}   