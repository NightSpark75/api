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
                select unique m.*
                from mpb_order_m m, mpa_dept_emp e, mpb_order_f f
                where m.deptno = e.deptno(+) and m.sno = f.sno 
                and (e.empno = '$user_id' or f.empno = '$user_id')
            ");
            return $this->success(['clearJob' => $clear]);
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
            $waiting = $this->getWaiting($deptno);
            $working = $this->getWorking($sno);

            $result = [
                'result' => true,
                'msg' => '取得清潔人員資料成功!(#0002)',
                'waiting' => $waiting,
                'working' => $working,
            ];
            return $result;
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }
    
    /**
     * 取得等待工作人員清單
     * 
     * @param string $sno 製程代號
     * @param string $psno 途程代號
     * @return array
     */
    private function getWaiting($deptno)
    {
        $waiting = DB::select("
            select *
            from stdadm.v_hra_emp_dept
            where deptno = '$deptno'
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
        $member = $this->getWorkingMember($sno);
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
        $member = DB::select("
            select * 
            from mpb_order_tw 
            where sno = :sno and psno = 0
        ", [
            'sno' => $sno,
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
            DB::insert("
                insert into mpb_order_tw 
                values (:sno, 0, :empno, '', sysdate)
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
            DB::delete("
                delete from mpb_order_tw 
                where sno = :sno and psno = 0 and empno = :empno
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
}   