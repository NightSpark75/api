<?php
/**
 * QA領料資料處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/07/24
 * @since 1.0.0 spark: 建立檔案寫入與讀取相關的資料處理
 * 
 */
namespace App\Repositories\MPE;

use DB;
use Exception;
use Auth;

/**
 * Class CatchlogRepository
 *
 * @package App\Repositories
 */
class QAReceiveRepository
{   
    public function __construct() {

    }

    public function getList()
    {
        $lsa_m = $this->get_lsa_m();
        $lsa_d = $this->get_lsa_d();
        $lsa_e = $this->get_lsa_e();
        $result = [
            'result' => true,
            'msg' => '取得領用資料成功!(#0001)',
            'lsa_m' => $lsa_m,
            'lsa_d' => $lsa_d,
            'lsa_e' => $lsa_e,
        ];
        return $result;
    }

    private function get_lsa_m($lsa_no = '%%')
    {
        $lsa_m = DB::select("
            select m.no, m.apply_user, au.ename uname, m.apply_unit, ad.dname dname,
                m.check_user, cu.ename cname, m.back_user, bu.empno bname, m.apply_date, 
                req_date, reason, doc_class, doc_no, back_date, back_reason, rdate, m.status
                , case when sum(case when e.status = 'Y' then 1 else 0 end) = count(e.barcode) then 'Y' else 'N' end posting
            from mpe_lsa_m m
                left join stdadm.v_hra_emp_dept1 au on m.apply_user = au.empno
                left join stdadm.v_hra_emp_dept1 ad on m.apply_unit = ad.deptno
                left join stdadm.v_hra_emp_dept1 cu on m.check_user = cu.empno
                left join stdadm.v_hra_emp_dept1 bu on m.back_user = bu.empno
                join mpe_lsa_e e on m.no = e.lsa_no
            where m.status = 'P' and m.no like :lsa_no
            group by m.no, m.apply_user, au.ename, m.apply_unit, ad.dname,
                m.check_user, cu.ename, m.back_user, bu.empno, m.apply_date, 
                req_date, reason, doc_class, doc_no, back_date, back_reason, rdate, m.status
            order by m.req_date, m.no
        ", ['lsa_no' => $lsa_no]);
        return $lsa_m;
    }

    private function get_lsa_d($lsa_no = '%%')
    {
        $lsa_d = DB::select("
            select d.lsa_no, d.bno, d.qty, d.partno, d.whouse, d.stor, m.pname, h.usize, h.unit 
                , case when sum(case when e.status = 'Y' then 1 else 0 end) = count(e.barcode) then 'Y' else 'N' end status
            from mpe_lsa_d d
                join mpe_mate m on d.partno = m.partno
                join mpe_house_m h on d.bno = h.batch and d.partno = h.partno 
                    and d.whouse = h.whouse and d.stor = h.stor 
                join mpe_lsa_e e on d.partno = e.partno and d.bno = e.bno 
                    and d.whouse = e.whouse and d.stor = e.stor and d.lsa_no = e.lsa_no
            where d.lsa_no like :lsa_no
            group by d.lsa_no, d.bno, d.qty, d.partno, d.whouse, d.stor, m.pname, h.usize, h.unit
            order by d.lsa_no, d.bno
        ", ['lsa_no' => $lsa_no]);
        return $lsa_d;
    }

    private function get_lsa_e($lsa_no = '%%')
    {
        $lsa_e = DB::select("
            select e.*, h.usize, h.unit, he.amt
            from mpe_lsa_e e
            join mpe_house_m h on e.bno = h.batch and e.partno = h.partno 
                    and e.whouse = h.whouse and e.stor = h.stor and h.code = '04'
            join mpe_house_e he on e.barcode = he.barcode
            where e.lsa_no like :lsa_no
            order by e.lsa_no, e.bno
        ", ['lsa_no' => $lsa_no]);
        return $lsa_e;
    }

    public function posting($no) 
    {
        try{
            DB::transaction( function () use($no) {
                $user = auth()->user()->id;
                $today = date('Ymd');
                DB::update("
                    update mpe_lsa_m
                        set status = 'R', rdate = $today
                        where no = :no
                ", ['no' => $no]);

                DB::update("
                    update mpe_lsa_e
                        set status = 'Y'
                        where lsa_no = :no
                ", ['no' => $no]);

                DB::update("
                    update mpe_house_e h
                        set sta = 'Z', duser = '$user'
                        where exists (
                            select barcode
                            from mpe_lsa_e e
                            where h.barcode = e.barcode
                                and e.lsa_no = :no
                        )
                ", ['no' => $no]);
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