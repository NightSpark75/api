<?php
/**
 * 生產派工流程資料
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/08/01
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
class WorkOrderRepository
{   
    use Sqlexecute;

    /**
     * construct
     * 
     * @return void
     */
    public function __construct() {

    }

    /**
     * 取得生產排程清單
     * 
     * @return array
     */
    public function getJob()
    {
        try{
            $user_id = auth()->user()->id;
            $where = $this->getOrderWhere($user_id);
            $order_d = DB::select("
                select d.*, 
                    pk_mpa.fu_pno_name(d.pno) pname, 
                    pk_mpa.fu_mno_name(d.mno) mname, 
                    pk_mpa.fu_rno_name(d.rno) rname
                from mpb_order_d d
                where $where
                order by d.sno, d.psno
            ");
            $result = [
                'result' => true,
                'msg' => '取得生產清單成功!(#0001)',
                'job_list' => $order_d,
            ];
            return $result;
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    /**
     * 取得生產排程查詢條件
     * 
     * @param string $user user id
     * @return string
     */
    private function getOrderWhere($user_id) {
        $type = DB::selectOne("
            select count(*) as count
            from mpa_dept_emp 
            where empno = '$user_id' 
        ");
        if ($type->count === 0) {
            $where = DB::selectOne("
                select pk_mpb.fu_order_where('1', '%', '$user_id') as str
                from dual
            ");
        } else {
            $where = DB::selectOne("
                select pk_mpb.fu_order_where('2', '', '$user_id') as str
                from dual
            ");
        }
        return $where->str;
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
            'result' => false,
            'msg' => '清單無更新',
        ];
    }

    /**
     * 取得途程生產人員清單
     * 
     * @param string $sno 製程代號
     * @param string $psno 途程代號
     * @return array
     */
    public function getMember($sno, $psno)
    {
        try {
            $prod = $this->getProcessInfo($sno, $psno);
            $rno = $prod->rno;
            $mno = $prod->mno;
            
            $waiting = $this->getWaiting($sno, $psno, $rno, $mno);
            $working = $this->getWorking($sno, $psno);

            $result = [
                'result' => true,
                'msg' => '取得生產人員資料成功!(#0002)',
                'waiting' => $waiting,
                'working' => $working,
            ];
            return $result;
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    /**
     * 取得該料品製造許可人員與設備
     * 
     * @param string $sno 製程代號
     * @param string $psno 途程代號
     * @param string $rno 房室代號
     * @return stdClass
     */
    private function getItmMember($sno, $psno, $rno)
    {
        $list = DB::select("
            select unique empno from (
                select empno 
                from mpa_proc_itm_f 
                where itm = pk_mpb.fu_oredr_d_msg('2',:sno,:psno) and 
                    bachno = pk_mpb.fu_oredr_d_msg('3',:sno,:psno) and psno = :psno
                union
                select empno 
                from mpa_room_emp 
                where rno = :rno)
        ", [
            'sno' => $sno,
            'psno' => $psno,
            'rno' => $rno,
        ]);
        return $list;
    }

    /**
     * 取得途程資訊
     * 
     * @param string $sno 製程代號
     * @param string $psno 途程代號
     * @return stdClass
     */
    private function getProcessInfo($sno, $psno)
    {
        $process = DB::selectOne("
            select *
            from mpb_order_d
            where sno = :sno and psno = :psno
        ", [
            'sno' => $sno,
            'psno' => $psno,
        ]);
        return $process;
    }
    
    /**
     * 檢查工作人員狀態
     * 
     * @param string $sno 製程代號
     * @param string $psno 途程代號
     * @param string $empno 員工代號
     * @return array
     */
    private function memberStateCheck($sno, $psno, $empno)
    {
        $result = DB::selectOne("
            select count(*) count
            from mpb_order_tw 
            where sno = :sno and psno = :psno and empno = :empno
        ", [
            'sno' => $sno,
            'psno' => $psno,
            'empno' => $empno,
        ]);
        if ((int)$result->count === 0) {
            return true;
        }
        return false;
    }

    /**
     * 檢查設備狀態
     * 
     * @param string $sno 製程代號
     * @param string $psno 途程代號
     * @param string $mno 機器代號
     * @return array
     */
    private function machineStateCheck($sno, $psno, $mno)
    {
        $result = DB::selectOne("
            select count(*) count
            from mpb_order_tw 
            where sno = :sno and psno = :psno and empno = :mno
        ", [
            'sno' => $sno,
            'psno' => $psno,
            'mno' => $mno,
        ]);
        if ((int)$result->count === 0) {
            return true;
        }
        return false;
    }

    /**
     * 加入人員資訊
     * 
     * @param array $list 人員清單
     * @param string $empno 員工代號
     * @return stdClass
     */
    private function pushMemberInfo($list, $empno)
    {
        $item = DB::selectOne("
            select
                :empno as empno,
                stdadm.pk_hra.fu_emp_name(:empno) ename
            from dual
        ", [
            'empno' => $empno,
        ]);
        if ($item->ename === '無') {
            $item->ename = $item->empno;
            $item->empno = '10'.$item->empno;
        } else {
            $item->empno.$item->ename;  
        }
        array_push($list, $item);
        return $list; 
    }
    
    /**
     * 取得等待工作人員清單
     * 
     * @param string $sno 製程代號
     * @param string $psno 途程代號
     * @return array
     */
    private function getWaiting($sno, $psno, $rno, $mno)
    {
        $waiting = [];
        $mno =  substr($mno, 2, 6);
        $member = $this->getItmMember($sno, $psno, $rno);
        for ($i = 0; $i < count($member); $i++) {
            $empno = $member[$i]->empno;
            $member_state = $this->memberStateCheck($sno, $psno, $empno);
            $waiting = $member_state ? $this->pushMemberInfo($waiting, $empno) : $waiting;
        }
        $machine_state = $this->machineStateCheck($sno, $psno, $mno);
        $waiting = $machine_state ? $this->pushMemberInfo($waiting, $mno) : $waiting;
        return $waiting;
    }

    /**
     * 取得工作人員清單
     * 
     * @param string $sno 製程代號
     * @param string $psno 途程代號
     * @return array
     */
    private function getWorking($sno, $psno)
    {
        $working = [];
        $member = $this->getWorkingMember($sno, $psno);
        for ($i = 0; $i < count($member); $i++) {
            $empno = $member[$i]->empno;
            $working = $this->pushMemberInfo($working, $empno);
        }
        return $working;
    }

    /**
     * 取得正在工作的人員
     * 
     * @param string $sno 製程代號
     * @param string $psno 途程代號
     * @return stdClass
     */
    private function getWorkingMember($sno, $psno)
    {
        $member = DB::select("
            select * 
            from mpb_order_tw 
            where sno = :sno and psno = :psno
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
            $params['empno'] = $this->formatNo($params['empno']);
            DB::insert("
                insert into mpb_order_tw 
                values (:sno, :psno, :empno, '', sysdate)
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
            $params['empno'] = $this->formatNo($params['empno']);
            DB::delete("
                delete from mpb_order_tw 
                where sno = :sno and psno = :psno and empno = :empno
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

    /**
     * 調整機器ID
     * 
     * @param string $empno
     * @return string
     */
    private function formatNo($empno)
    {
        if (substr($empno, 0, 3) === '10M') {
            return substr($empno, 2, 6);
        }
        return $empno;
    }

    public function allJoinWorking($params)
    {
        try{
            $sno = $params['sno'];
            $psno = $params['psno'];
            $prod = $this->getProcessInfo($sno, $psno);
            $rno = $prod->rno;
            $mno = substr($prod->mno, 2, 6);
            $member = $this->getItmMember($sno, $psno, $rno);
            for ($i = 0; $i < count($member); $i++) {
                $empno = $member[$i]->empno;
                $params['empno'] = $empno;
                $this->joinWorking($params, $this->memberStateCheck($sno, $psno, $empno));
            }
            $params['empno'] = $mno;
            $this->joinWorking($params, $this->machineStateCheck($sno, $psno, $mno));

            $result = [
                'result' => true,
                'msg' => '整批報工成功!(#0004)',
            ];
            return $result;
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    public function allLeaveWorking($params)
    {
        try {
            $sno = $params['sno'];
            $psno = $params['psno'];
            $member = $this->getWorkingMember($sno, $psno);
            for ($i = 0; $i < count($member); $i++) {
                $empno = $member[$i]->empno;
                $params['empno'] = $empno;
                $this->leaveWorking($params);
            }
            $result = [
                'result' => true,
                'msg' => '整批結束報工成功!(#0005)',
            ];
            return $result;
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    public function workComplete($params)
    {
        try{
            DB::transaction( function () use($params) {
                DB::delete("
                    delete from mpb_order_tw
                    where sno = :sno and psno = :psno
                ", [
                    'sno' => $params['sno'],
                    'psno' => $params['psno'],
                ]);

                DB::update("
                    update mpb_order_d
                    set state = 'Y', clean = :clean
                    where sno = :sno and psno = :psno
                ", $params);

                DB::delete("
                    delete from mpb_proc_now
                    where sno = :sno
                ", ['sno' => $params['sno']]);
                $pdo = DB::getPdo();
                $stmt = $pdo->prepare("begin pk_mpb.proc_create_next_proc(:sno); end;");
                $stmt->bindParam(':sno', $params['sno']);
                $stmt->execute();
            });
            DB::commit();
            $result = [
                'result' => true,
                'msg' => '完成工作!(#0006)',
            ];
            return $result;
        } catch (Exception $e) {
            DB::rollback();
            return $this->exception($e);
        }
    }
}   