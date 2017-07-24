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
use App\Traits\Sqlexecute;

use App\Models\MPE\MPE_LSA_M;
use App\Models\MPE\MPE_LSA_D;
use App\Models\MPE\MPE_LSA_E;


/**
 * Class CatchlogRepository
 *
 * @package App\Repositories
 */
class QAReceiveRepository
{   
    use Sqlexecute;
    
    private $lsa_m;
    private $lsa_d;
    private $lsa_e;

    public function __construct(
        MPE_LSA_M $lsa_m,
        MPE_LSA_D $lsa_d,
        MPE_LSA_E $lsa_e
    ) {
        $this->lsa_m = $lsa_m;
        $this->lsa_d = $lsa_d;
        $this->lsa_e = $lsa_e;
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

    public function getDetail($lsa_no)
    {
        $lsa_m = $this->get_lsa_m($lsa_no);
        $lsa_d = $this->get_lsa_d($lsa_no);
        $lsa_e = $this->get_lsa_e($lsa_no);
        $result = [
            'result' => true,
            'msg' => '取得領用申請詳細資料成功!(#0002)',
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
                req_date, reason, doc_class, doc_no, back_date, back_reason, rdate, status
            from mpe_lsa_m m
                left join stdadm.v_hra_emp_dept1 au on m.apply_user = au.empno
                left join stdadm.v_hra_emp_dept1 ad on m.apply_unit = ad.deptno
                left join stdadm.v_hra_emp_dept1 cu on m.check_user = cu.empno
                left join stdadm.v_hra_emp_dept1 bu on m.back_user = bu.empno
            where m.status = 'P' and m.no like :lsa_no
            group by m.no, m.apply_user, au.ename, m.apply_unit, ad.dname,
                m.check_user, cu.ename, m.back_user, bu.empno, m.apply_date, 
                req_date, reason, doc_class, doc_no, back_date, back_reason, rdate, status
            order by m.req_date
        ", ['lsa_no' => $lsa_no]);
        return $lsa_m;
    }

    private function get_lsa_d($lsa_no = '%%')
    {
        $lsa_d = DB::select("
            select d.*, m.pname, h.usize, h.unit
            from mpe_lsa_d d
                join mpe_mate m on d.partno = m.partno
                join mpe_house_m h on d.bno = h.batch and d.partno = h.partno 
                    and d.whouse = h.whouse and d.stor = h.stor 
            where d.lsa_no like :lsa_no
            order by d.bno
        ", ['lsa_no' => $lsa_no]);
        return $lsa_d;
    }

    private function get_lsa_e($lsa_no = '%%')
    {
        $lsa_e = DB::select("
            select e.*, m.pname, h.usize, h.unit, he.amt
            from mpe_lsa_e e
                join mpe_mate m on e.partno = m.partno
                join mpe_house_m h on e.bno = h.batch and e.partno = h.partno 
                    and e.whouse = h.whouse and e.stor = h.stor and h.code = '04'
                join mpe_house_e he on e.bno = he.batch and e.partno = he.partno 
                    and e.whouse = he.whouse and e.stor = he.stor and h.code = '04'
                        where e.lsa_no like :lsa_no
                order by e.barcode
        ", ['lsa_no' => $lsa_no]);
        return $lsa_e;
    }

    public function posting() 
    {

    }
}   