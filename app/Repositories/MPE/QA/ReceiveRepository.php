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
class ReceiveRepository
{   
    use Sqlexecute;
    
    public function __construct() 
    {

    }

    public function getList()
    {
        $lsa_m = $this->get_lsa_m();
        $result = [
            'result' => true,
            'msg' => '取得領用資料成功!(#0001)',
            'lsa_m' => $lsa_m,
        ];
        return $result;
    }

    public function getDetail($lsa_no)
    {
        $lsa_d = $this->get_lsa_d($lsa_no);
        $lsa_e = $this->get_lsa_e($lsa_no);
        $result = [
            'result' => true,
            'msg' => '取得領用詳細資料成功!(#0003)',
            'lsa_d' => $lsa_d,
            'lsa_e' => $lsa_e,
        ];
        return $result;
    }

    private function get_lsa_m($lsa_no = '%%')
    {
        $lsa_m = DB::select("
            select m.no, m.apply_user, stdadm.pk_hra.fu_emp_name(m.apply_user) uname, m.apply_unit, stdadm.pk_hra.fu_dept_name(m.apply_unit) dname,
                m.check_user, stdadm.pk_hra.fu_emp_name(m.check_user) cname, m.back_user, stdadm.pk_hra.fu_emp_name(m.back_user) bname, m.apply_date, 
                req_date, reason, doc_class, doc_no, back_date, back_reason, rdate, m.status
                from mpe_lsa_m m
                where m.status = 'P' and m.no like :lsa_no
                group by m.no, m.apply_user, m.apply_unit,
                    m.check_user, m.back_user, m.apply_date, 
                    req_date, reason, doc_class, doc_no, back_date, back_reason, rdate, m.status
                order by m.req_date, m.no
        ", ['lsa_no' => $lsa_no]);
        return $lsa_m;
    }

    private function get_lsa_d($lsa_no = '%%')
    {
        $lsa_d = DB::select("
            select d.lsa_no, d.bno, d.qty, d.partno, d.whouse, d.stor, m.pname, h.usize, h.unit 
                , d.status
                from mpe_lsa_d d
                    join mpe_mate m on d.partno = m.partno
                    join mpe_house_m h on d.bno = h.batch and d.partno = h.partno 
                        and d.whouse = h.whouse and d.stor = h.stor 
                    join mpe_lsa_m lm on lm.no = d.lsa_no
                where d.lsa_no like :lsa_no and lm.status = 'P'
                group by d.lsa_no, d.bno, d.qty, d.partno, d.whouse, d.stor, m.pname, h.usize, h.unit, d.status
                order by d.lsa_no, d.bno
        ", ['lsa_no' => $lsa_no]);
        return $lsa_d;
    }

    private function get_lsa_e($lsa_no = '%%')
    {
        $lsa_e = DB::select("
            select m.no lsa_no, e.*, h.usize, h.unit
                from mpe_house_e e, mpe_lsa_m m, mpe_lsa_d d, mpe_house_m h
                where e.sta = 'N'
                    and m.status = 'P' and m.no = d.lsa_no and m.no like :lsa_no
                    and e.partno = h.partno and e.batch = h.batch
                    and e.whouse = h.whouse and e.stor = h.stor
                    and e.partno = d.partno and e.batch = d.bno
                    and e.whouse = d.whouse and e.stor = d.stor
                    order by m.no, e.batch
        ", ['lsa_no' => $lsa_no]);
        return $lsa_e;
    }

    public function posting($no, $item_e) 
    {
        try{
            DB::transaction( function () use($no, $item_e) {
                $user = auth()->user()->id;
                $today = date('Ymd');
                DB::update("
                    update mpe_lsa_m
                        set status = 'R', rdate = $today, suser = '$user'
                        where no = :no
                ", ['no' => $no]);

                for ($i = 0; $i < count($item_e); $i++) {
                    $item = $item_e[$i];
                    if ($item['sta'] === 'Y') {
                        $binds = [
                            'lsa_no' => $item['lsa_no'],
                            'bno' => $item['batch'],
                            'barcode' => $item['barcode'],
                            'status' => $item['sta'],
                            'partno' => $item['partno'],
                            'whouse' => $item['whouse'],
                            'stor' => $item['stor'],
                            'qty' => $item['amt'],
                        ];
                        DB::insert("
                            insert into mpe_lsa_e
                                (lsa_no, bno, barcode, status, partno, whouse, stor, qty)
                            values
                                (:lsa_no, :bno, :barcode, :status, :partno, :whouse, :stor, :qty)
                        ", $binds);
                    }
                } 

                DB::update("
                    update mpe_house_e h
                        set sta = 'Y', duser = '$user', ddate = sysdate, rmk = '領用單：$no'
                        where exists (
                            select barcode
                            from mpe_lsa_e e
                            where h.barcode = e.barcode
                                and e.lsa_no = :no
                        )
                ", ['no' => $no]);
                
                $rec_no = DB::selectOne("select pk_mpe.fu_create_no('PI') rec_no from dual")->rec_no;
                $dept_no = DB::selectOne("select deptno from stdadm.v_hra_emp_dept1 where empno = '$user'")->deptno;
        
                DB::insert("
                    insert into mpe_rec_m
                        (code, sinnum, typ, stat, rmk, ouser, odept, odate, post, duser, ddate, ldate)
                    values 
                        ('04', '$rec_no', 'A', '', '領料單號：$no', '$user', '$dept_no', to_number(to_char(sysdate, 'YYYYMMDD'))
                            ,'Y', '$user', sysdate, to_number(to_char(sysdate, 'YYYYMMDD')))
                ");
                
                DB::insert("
                    insert into mpe_rec_d
                        (code, sinnum, barcode, partno, whouse, stor, grid, batch, rmk, duser, ddate, usize)
                    select '04', '$rec_no', h.barcode, h.partno, h.whouse, h.stor, h.grid, h.batch,  '領用:'||h.amt, '$user', sysdate, h.amt
                        from mpe_lsa_e e, mpe_house_e h
                        where e.barcode = h.barcode and e.lsa_no = '$no'
                ");
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

    public function getCheckList()
    {
        $lsa_m = DB::select("
            select m.no, m.apply_user, stdadm.pk_hra.fu_emp_name(m.apply_user) uname, m.apply_unit, stdadm.pk_hra.fu_dept_name(m.apply_unit) dname,
                    m.check_user, stdadm.pk_hra.fu_emp_name(m.check_user) cname, m.back_user, stdadm.pk_hra.fu_emp_name(m.back_user) bname, m.apply_date, 
                    req_date, reason, doc_class, doc_no, back_date, back_reason, rdate, m.status
                from mpe_lsa_m m
                where m.status = 'R'
                group by m.no, m.apply_user, m.apply_unit,
                    m.check_user, m.back_user, m.apply_date, 
                    req_date, reason, doc_class, doc_no, back_date, back_reason, rdate, m.status
                order by m.req_date, m.no
        ");
        return $this->success(compact('lsa_m'));
    }

    public function getCheckDetail($no)
    {
        $lsa_d = DB::select("
            select d.lsa_no, d.bno, d.qty, d.partno, d.whouse, d.stor, m.pname, h.usize, h.unit, d.status
                from mpe_lsa_d d
                    join mpe_mate m on d.partno = m.partno
                    join mpe_house_m h on d.bno = h.batch and d.partno = h.partno 
                        and d.whouse = h.whouse and d.stor = h.stor 
                    join mpe_lsa_m lm on lm.no = d.lsa_no
                where d.lsa_no = :no and lm.status = 'R'
                group by d.lsa_no, d.bno, d.qty, d.partno, d.whouse, d.stor, m.pname, h.usize, h.unit, d.status
                order by d.lsa_no, d.bno
        ", compact('no'));

        $lsa_e = DB::select("
            select e.partno, e.bno, pk_mpe.fu_pname(e.partno) pname, 
                    pk_mpe.fu_posit(e.whouse) posit, pk_mpe.fu_storn(e.whouse, e.stor) storn, d.qty, e.barcode, 
                    e.qty, pk_mpe.fu_get_usize(e.partno, e.bno, e.whouse, e.stor, 'N') usize, pk_mpe.fu_get_unit(e.partno, e.bno, e.whouse, e.stor, 'N') unit
                from mpe_lsa_m m, mpe_lsa_d d, mpe_lsa_e e
                where m.no = :no and m.no = d.lsa_no
                    and d.partno = e.partno and d.bno = e.bno
                    and d.whouse = e.whouse and d.stor = e.stor
        ", compact('no'));
        
        return $this->success(compact('lsa_d', 'lsa_e'));
    }

    public function confirm($no, $receive_user)
    {
        try {
            DB::update("
                update mpe_lsa_m
                    set status = 'S', receive_user = :receive_user,
                        receive_date = pk_date.fu_number(sysdate)
                    where no = :no and status = 'R'
            ", compact('receive_user', 'no'));

            DB::update("
                update mpe_lsa_e
                    set status = 'R' 
                    where lsa_no = :no and status = 'Y'
            ", compact('no'));

            return $this->success();
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    public function checkUser($empno)
    {
        try{
            $user = DB::selectOne("
                select empno, ename, deptno, dname
                    from stdadm.v_hra_emp_dept
                    where empno = :empno
            ", compact('empno'));
            if ($user) {
                return $this->success(compact('user'));
            }
            throw New Exception('找不到員工資料!');
        } catch(Exception $e) {
            return $this->exception($e);
        }
    }
}   