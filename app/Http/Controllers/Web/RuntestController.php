<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Exception;
use App\Traits\Common;
use App\Services\MPE\QA\OverdueService;
use DB;

class RuntestController extends Controller
{
    use Common;

    private $overdue;

    public function __construct(OverdueService $overdue)
    {
        $this->overdue = $overdue;
    }
    
    public function test()
    {
        //$a = $this->overdue->overdueNotice();
        $list = DB::select("
            select m.no, m.doc_class, m.reason, e.barcode, e.partno, pk_mpe.fu_pname(e.partno) pname
                    , e.bno, m.apply_user, stdadm.pk_hra.fu_emp_name(m.apply_user) apply_ename
                    , stdadm.pk_hra.fu_emp_email(m.apply_user) aemail
                    , m.receive_user, stdadm.pk_hra.fu_emp_name(m.apply_user) receive_ename
                    , stdadm.pk_hra.fu_emp_email(m.receive_user) remail, m.rdate
                from mpe_lsa_e e, mpe_lsa_m m 
                where e.status = 'R' and e.lsa_no = m.no 
                    and pk_date.fu_number(sysdate) >= stdadm.pk_hra.fu_work_day('ALL', m.rdate, 7)
                order by m.no, m.rdate
        ");
        return $list;
    }
}
