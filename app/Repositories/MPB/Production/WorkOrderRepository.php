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

/**
 * Class WorkOrderRepository
 *
 * @package App\Repositories\MPB\Production
 */
class WorkOrderRepository
{   
    public function __construct() {

    }

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
            $result = [
                'result' => false,
                'msg' => $e->getMessage(),
            ];
            return $result;
        }
    }

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

    public function compare($job_list) 
    {
        try {
            $current = json_decode(json_encode($this->getJob()['job_list']), true);
            return $this->compareCount($job_list, $current);
        } catch (Exception $e) {
            $result = [
                'result' => false,
                'msg' => $e->getMessage(),
            ];
            return $result;
        }
    }
    
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

    public function getMember($sno, $psno)
    {
        try {
            $waiting = [];
            $member_list = [];
            $member = $this->getItmMember($sno, $psno);
            for ($i = 0; $i < count($member); $i++) {
                $empno = $member[$i]->empno;
                $state = $this->memberStateCheck($sno, $psno, $empno);
                $member_list = $state ? $this->getWaitingList($member_list, $empno) : $member_list;
                //*****
            }

            $result = [
                'result' => true,
                'msg' => '取得生產人員資料成功!(#0002)',
                'waiting' => $waiting,
            ];
            return $result;
        } catch (Exception $e) {
            $result = [
                'result' => false,
                'msg' => $e->getMessage(),
            ];
            return $result;
        }
        
    }

    private function getItmMember($sno, $psno, $rno = null)
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

    private function machineStateCheck($sno, $psno, $mno)
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
    
    private function setMember($list, $empno)
    {
        $item = DB::selectOne("
            select 
                :empno as empno,
                stdadm.pk_hra.fu_emp_name(:empno) ename
            from dual
        ", ['empno' => $empno]);
        array_push($list, $item);
        return $list;
    }
}   