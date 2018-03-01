<?php
/**
 * 生產派工流程資料
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/08/01
 * @since 1.0.0 spark: 建立檔案寫入與讀取相關的資料處理
 * @since 1.1.0 spark: 依上線前需求修正
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
class ProductionRepository
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
            //$where = $this->getProductionWhere($user_id);
            $order_d = DB::select("
                select d.*, 
                    pk_mpa.fu_pno_name(d.pno) pname, 
                    pk_mpa.fu_mno_name(d.mno) mname, 
                    pk_mpa.fu_rno_name(d.rno) rname,
                    pk_mpb.fu_oredr_m_msg('1', d.sno)||chr(13)||pk_mpb.fu_oredr_m_msg('2', d.sno)||chr(13)||
                        '用料號='||pk_mpb.fu_oredr_d_msg('1', d.sno, d.psno) as info
                    , pk_mpb.fu_check_litm(d.sno, d.psno) check_litm
                from v_mpb_order_prod d
                where empno = '$user_id'
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
    private function getProductionWhere($user_id) {
        $type = DB::selectOne("
            select count(*) as count
            from mpa_dept_emp
            where empno = '$user_id' 
        ");
        if ((int)$type->count === 0) {
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
        if ($where->str === 'x') {
            return '1 = 2';
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
            'result' => true,
            'msg' => '清單無更新',
            'job_list' => $current,
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
            $non_check = DB::selectOne("select pk_mpb.fu_check_litm('$sno', $psno) v_check from dual")->v_check;
            if ($non_check > 0) {
                return $this->success(['non_check' => true]);
            }

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
                select empno from mpb_order_f 
                    where sno = :sno and psno = :psno 
                union
                select mno empno from mpb_order_g 
                    where sno = :sno and psno = :psno
                )
        ", [
            'sno' => $sno,
            'psno' => $psno,
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
            where empno = :empno
        ", [
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
            where sno = :sno and psno = :psno and empno = substr(:mno, 3, 7)
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
            if (substr($item->empno, 0, 3) === '10M') {
                $item->ename = substr($item->empno, 2, 7);
            } else {
                $item->ename = $item->empno;
                $item->empno = '10'.$item->empno;
            }
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
        $member = $this->getItmMember($sno, $psno, $rno);
        for ($i = 0; $i < count($member); $i++) {
            $empno = $member[$i]->empno;
            $member_state = $this->memberStateCheck($sno, $psno, $empno);
            $waiting = $member_state ? $this->pushMemberInfo($waiting, $empno) : $waiting;
        }
        /*
        if ($mno !== null) {
            $machine_state = $this->machineStateCheck($sno, $psno, $mno);
            $waiting = $machine_state ? $this->pushMemberInfo($waiting, substr($mno, 2, 6)) : $waiting;
        }
        */
        return $waiting;
    }

    /**
     * 取得工作人員清單
     * 
     * @param string $sno 製程代號
     * @param string $psno 途程代號
     * @return array
     * 
     * :WARNING: 20170908 發現會有工作人員無法移出情形
     * 詳細為 PPMADM.TR_MPB_ORDER_TW_D 會判斷 mpb_order_tw.rmk = '1' 
     * 才能刪除工作中人員資料
     * 故在此增加程式以致可以順利刪除
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
            //$params['empno'] = $this->formatNo($params['empno']);
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
            //$params['empno'] = $this->formatNo($params['empno']);
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
            $mno = $prod->mno;
            $member = $this->getItmMember($sno, $psno, $rno);
            for ($i = 0; $i < count($member); $i++) {
                $empno = $member[$i]->empno;
                $params['empno'] = $empno;
                $this->joinWorking($params, $this->memberStateCheck($sno, $psno, $empno));
            }
            if ($mno !== null) {
                $params['empno'] = substr($mno, 2, 6);
                $this->joinWorking($params, $this->machineStateCheck($sno, $psno, substr($mno, 2, 6)));
            }
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
                    set state = 'Y'
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

    public function getMaterial($sno, $psno)
    {
        try {
            $info = DB::selectOne("
                select pk_mpb.fu_oredr_m_msg('1', 'SP17000003') minfo
                    ,pk_mpb.fu_oredr_m_msg('2', 'SP17000003') sinfo
                    ,pk_mpb.fu_oredr_d_msg('1', 'SP17000003', 300) mainfo
                from dual
            ");
            $material = DB::select("
                select *
                from mpb_order_e
                where sno = '$sno' and psno = $psno
            ");
            return $this->success(['info' => $info, 'material' => $material]);
        } catch (Exception $e) {
            return $this->exception($e);
        }

    }

    public function checkMaterial($sno, $psno)
    {
        try {
            // 如果已經完成領料確認程序，即不再更新領料確認資訊
            $isCheck = DB::selectOne("
                select count(*) v_check
                    from mpb_order_e
                    where sno = '$sno' and psno = $psno and ukid is null
            ")->v_check;

            if ($isCheck > 0) {
                $user = auth()->user()->id;
                DB::update("
                    update mpb_order_e
                        set ukid = '$user', duser = '$user', ddate = sysdate
                        where sno = '$sno' and psno = $psno
                ");
            }
            return $this->success();
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    public function getClearJob()
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
}   