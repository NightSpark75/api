<?php
/**
 * 生產派工流程資料
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/11/16
 * @since 1.0.0 spark: 建立檔案寫入與讀取相關的資料處理
 * 
 */
namespace App\Repositories\MPB\Production;

use DB;
use Exception;
use Auth;
use App\Traits\Sqlexecute;

/**
 * Class WorkOrderRepository
 *
 * @package App\Repositories\MPB\Production
 */
class CleanRepository
{   
    use Sqlexecute;

    /**
     * construct
     * 
     * @return void
     */
    public function __construct() {

    }

    public function getJob()
    {
        try {
            $user_id = auth()->user()->id;
            $clear = DB::select("
                select m.sno, m.bno, m.iname, m.item
                    from mpb_order_m m, mpa_dept_emp e, mpb_order_f f
                    where m.deptno = e.deptno(+) and m.sno = f.sno 
                        and (e.empno = '$user_id' or f.empno = '$user_id') and state in ('Y', 'O')
                    group by m.sno, m.bno, m.iname, m.item
                    order by m.sno desc
            ");
            return $this->success(['job_list' => $clear]);
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    /**
     * 比對生產排程清單是否有更新，並回傳結果
     * 
     * @param array $job_list 生產排程清單
     * @return array
     */
    public function compare($job_list) 
    {
        try {
            $current = json_decode(json_encode($this->getJob()['job_list']), true);
            return $this->compareCount($job_list, $current);
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }
    
    /**
     * 比較兩個生產清單的資料筆數
     * 
     * @param array $job 比較的清單
     * @param array $current 從資料庫查詢回來的清單
     * @return array
     */
    private function compareCount($job, $current)
    {
        if (count($job) === count($current)) {
            return $this->compareContent($job, $current);
        }
        return [
            'result' => true,
            'msg' => '更新生產清單',
            'job_list' => $current,
        ];
    }

    /**
     * 比較兩個生產清單的內容
     * 
     * @param array $job 比較的清單
     * @param array $current 從資料庫查詢回來的清單
     * @return array
     */
    private function compareContent($job, $current) 
    {
        for ($i = 0; $i < count($job); $i++) {
            if (count(array_diff($job[$i], $current[$i])) > 0) {
                return [
                    'result' => true,
                    'msg' => '更新生產清單',
                    'job_list' => $current,
                ];
            }
        }
        return [
            'result' => true,
            'msg' => '清單無更新',
            'job_list' => $current,
        ];
    }

    public function getDept($deptno)
    {
        try {
            $dept_list = DB::select("
                select unique deptno, dname
                    from stdadm.v_hra_emp_dept
                    where deptno like substr('$deptno', 1, 4)||'%' and deptno <> '$deptno'
            ");
            return $this->success(['dept_list' => $dept_list]);
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    /**
     * 取得途程生產人員清單
     * 
     * @param string $sno 製程代號
     * @param string $deptno 單位代號
     * @return array
     */
    public function getMember($sno, $deptno)
    {
        try {         
            $waiting = $this->getWaiting($sno, $deptno);
            $working = $this->getWorking($sno);
            $prod = $this->getProcessInfo($sno);

            $result = [
                'result' => true,
                'msg' => '取得清潔人員資料成功!(#0002)',
                'waiting' => $waiting,
                'working' => $working,
                'prod' => $prod,
            ];
            return $result;
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    /**
     * 取得資訊
     * 
     * @param string $sno 製程代號
     * @return stdClass
     */
    private function getProcessInfo($sno)
    {
        $process = DB::selectOne("
            select *
                from mpb_order_m
                where sno = :sno
        ", [
            'sno' => $sno,
        ]);
        return $process;
    }
    
    /**
     * 取得等待工作人員清單
     * 
     * @param string $sno 製程代號
     * @param string $psno 途程代號
     * @return array
     */
    private function getWaiting($sno, $deptno)
    {
        $psno = $this->getFirstPsno($sno);
        $waiting = DB::select("
            select v.*
                from stdadm.v_hra_emp_dept v
                where v.deptno = '$deptno' and not exists
                    (select * 
                        from mpb_order_tw t 
                        where t.sno = '$sno' and psno = $psno and t.empno = v.empno and rmk = '2'
                    )
        ");
        return $waiting;
    }

    /**
     * 取得工作人員清單
     * 
     * @param string $sno 製程代號
     * @return array
     * 
     * :WARNING: 20170908 發現會有工作人員無法移出情形
     * 詳細為 PPMADM.TR_MPB_ORDER_TW_D 會判斷 mpb_order_tw.rmk = '1' 
     * 才能刪除工作中人員資料
     * 故在此增加程式以致可以順利刪除
     */
    private function getWorking($sno)
    {
        $working = [];
        $working = $this->getWorkingMember($sno);
        return $working;
    }

    /**
     * 取得正在工作的人員
     * 
     * @param string $sno 製程代號
     * @return stdClass
     */
    private function getWorkingMember($sno)
    {
        $psno = $this->getFirstPsno($sno);
        $member = DB::select("
            select t.*, stdadm.pk_hra.fu_emp_name(t.empno) ename
                from mpb_order_tw t
                where t.sno = :sno and t.psno = :psno and rmk = '2'
        ", [
            'sno' => $sno,
            'psno' => $psno,
        ]);
        return $member;
    }

    /**
     * 開始報工
     * 
     * @param array $params 參數
     * @return stdClass
     */
    public function joinWorking($params, $check = true)
    {
        try{
            if (!$check) {
                return null;
            }
            $params['psno'] = $this->getFirstPsno($params['sno']);
            DB::insert("
                insert into mpb_order_tw 
                    values (:sno, :psno, :empno, '2', sysdate)
            ", $params);
            $result = [
                'result' => true,
                'msg' => '開始報工成功!(#0002)',
            ];
            return $result;
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    /**
     * 結束報工
     * 
     * @param array $params 參數
     * @return stdClass
     */
    public function leaveWorking($params)
    {
        try{
            $params['psno'] = $this->getFirstPsno($params['sno']);
            DB::delete("
                delete from mpb_order_tw 
                    where sno = :sno and psno = :psno and empno = :empno and rmk = '2'
            ", $params);
            $result = [
                'result' => true,
                'msg' => '結束報工成功!(#0003)',
            ];
            return $result;
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }    

    public function getFirstPsno($sno)
    {
        $psno = DB::selectOne("
            select min(psno) psno
                from mpb_order_d
                where sno = '$sno'
        ")->psno;
        return $psno;
    }
}   