<?php
/**
 * qa receive overdue notice service
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/02/05
 * @since 1.0.0 spark: handle overdue notice service
 * 
 */
namespace App\Services\MPE\QA;

use App\Repositories\MPE\QA\StockRepository;
use Exception;
use DB;

/**
 * Class OverdueService
 *
 * @package App\Services
 */
class OverdueService {

    /**
     * get overdue list at today
     * 
     * @return array
     */
    private function overdueList() {
        $list = DB::select("
            select m.no, m.doc_class, m.reason, e.barcode, e.partno, pk_mpe.fu_pname(e.partno) pname
                    , e.bno, m.apply_user, stdadm.pk_hra.fu_emp_name(m.apply_user) apply_ename
                    , stdadm.pk_hra.fu_emp_email(m.apply_user) aemail
                    , m.receive_user, stdadm.pk_hra.fu_emp_name(m.receive_user) receive_ename
                    , stdadm.pk_hra.fu_emp_email(m.receive_user) remail, m.rdate
                from mpe_lsa_e e, mpe_lsa_m m 
                where e.status = 'R' and e.lsa_no = m.no 
                    and pk_date.fu_number(sysdate) >= stdadm.pk_hra.fu_work_day('ALL', m.rdate, 7)
                order by m.no, m.rdate
        ");
        return $list;
    }

    /**
     * handle overdue notice to apply user, receiver and administrator
     * 
     * @return mixed
     */
    public function overdueNotice() {
        try {
            $today = date('Ymd');
            $workDate = $this->getWorkDate($today, 1);
            $applicant = 0;
            $receiver = 0;
            $admin = 0;
            if ($today === $workDate) {
                $list = json_decode(json_encode($this->overdueList()), true);
                $auser = $this->uniqueUser($list, 'apply_user');
                $ruser = $this->uniqueUser($list, 'receive_user');
                //$applicant = $this->applyUserNotice($list, $auser);
                //$receiver = $this->receiveUserNotice($list, $ruser);
                $admin = $this->adminNotice($list);
                $logs = "send mail apply = $applicant, receive = $receiver, admin = $admin";
                return $logs;
            }
            return 'today is holiday';
        } catch (Exception $e) {
            return 'exception: '.$e->getMessage();
        }
    }


    /**
     * get work date
     * 
     * @param int $date
     * @param int $day
     * @return int
     */
    private function getWorkDate($date, $day) {
        $workDate = DB::selectOne("
            select stdadm.pk_hra.fu_work_day('ALL', $date, $day) d from dual
        ")->d;
        return $workDate;
    }

    /**
     * unique user and return 1d array
     * 
     * @param array $list
     * @param string $user
     * @return array
     */
    private function uniqueUser($list, $user)
    {
        $arr = [];
        $ind = 0;
        for ($i = 0; $i < count($list); $i++ ) {
            array_push($arr, $list[$i][$user]);
        }
        $uni = array_values(array_unique($arr));
        return $uni;
    }

    /**
     * handle apply user notice
     * 
     * @param array $list
     * @param array $user
     * @return int
     */
    private function applyUserNotice($list, $user)
    {
        $count = 0;
        for ($i = 0; $i < count($user); $i++) {
            $overList = $this->applyUserOverdueList($list, $user[$i]);
            $count = $count + $this->sendApplyOverdueMail($overList);
        }
        return $count;
    }

    /**
     * handle receiver notice
     * 
     * @param array $list
     * @param array $user
     * @return int
     */
    private function receiveUserNotice($list, $user)
    {
        $count = 0;
        for ($i = 0; $i < count($user); $i++) {
            $overList = $this->receiveUserOverdueList($list, $user[$i]);
            $count = $count + $this->sendReceiveOverdueMail($overList);       
        }
        return $count;
    }

    /**
     * handle administrator notice
     * 
     * @param array $list
     * @return int
     */
    private function adminNotice($list)
    {
        $user = DB::selectOne("
            select empno from mpe_mail_set where role = 'CM'
        ")->empno;
        return $this->sendOverdueList($list, $user);
    }

    /**
     * get overdue list by apply user
     * 
     * @param array $list
     * @param array $user
     * @return array
     */
    private function applyUserOverdueList($list, $user)
    {
        $overList = [];
        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]['apply_user'] === $user) {
                array_push($overList, $list[$i]);
            }
        }
        return $overList;
    }

    /**
     * get overdue lsit by receiver
     * 
     * @param array $list
     * @param array $user
     * @return array
     */
    private function receiveUserOverdueList($list, $user)
    {
        $overList = [];
        for ($i = 0; $i< count($list); $i++) {
            if ($list[$i]['receive_user'] === $user 
                && $list[$i]['receive_user'] !== $list[$i]['apply_user']) {
                array_push($overList, $list[$i]);
            }
        }
        return $overList;
    }

    /**
     * set mail content of apply user's overdue list
     * 
     * @param array $list
     * @return int
     */
    private function sendApplyOverdueMail($list)
    {
        if (count($list) > 0) {
            $subject = '留樣品逾期歸還通知!(申請)';
            $sender = 'qa.inventory@standard.com.tw';
            $recipient = $list[0]['aemail'];
            //$recipient = 'Lin.Yupin@standard.com.tw';
            $head = ['申請單號', '附件類別', '申請事由', '條碼號', '料號', '品名', '批號', '收樣日期'];
            $key = ['no', 'doc_class', 'reason', 'barcode', 'partno', 'pname', 'bno', 'rdate'];
            $content = $this->mailContent($list, $head, $key);
            $this->sendMail($subject, $sender, $recipient, $content);
            return 1;
        }
        return 0;
    }

    /**
     * set mail content of receiver's overdue list
     * 
     * @param array $list
     * @return int
     */
    private function sendReceiveOverdueMail($list)
    {
        if (count($list) > 0) {
            $subject = '留樣品逾期歸還通知!(收樣)';
            $sender = 'qa.inventory@standard.com.tw';
            $recipient = $list[0]['remail'];
            //$recipient = 'Lin.Yupin@standard.com.tw';
            $head = ['申請單號', '申請人', '姓名', '附件類別', '申請事由', '條碼號', '料號', '品名', '批號', '收樣日期'];
            $key = ['no', 'apply_user', 'apply_ename', 'doc_class', 'reason', 'barcode', 'partno', 'pname', 'bno', 'rdate'];
            $content = $this->mailContent($list, $head, $key);
            $this->sendMail($subject, $sender, $recipient, $content);
            return 1;
        }
        return 0;
    }

    /**
     * set mail content of overdue list for administrator
     * 
     * @param array $list
     * @return int
     */
    private function sendOverdueList($list, $user)
    {
        if (count($list) > 0) {
            $subject = '留樣品逾期歸還清單!';
            $sender = 'qa.inventory@standard.com.tw';
            $recipient = $user;
            //$recipient = 'Lin.Yupin@standard.com.tw';
            $head = ['申請單號', '申請人', '姓名', '附件類別', '申請事由', '條碼號', '料號', '品名', '批號', '收樣日期', '收樣人', '姓名'];
            $key = ['no', 'apply_user', 'apply_ename', 'doc_class', 'reason', 'barcode', 'partno', 'pname', 'bno', 'rdate', 'receive_user', 'receive_ename'];
            $content = $this->mailContent($list, $head, $key);
            $this->sendMail($subject, $sender, $recipient, $content);
            return 1;
        }
        return 0;
    }

    /**
     * build mail content
     * 
     * @param array $list
     * @param array $head
     * @param array $key
     * @return string
     */
    private function mailContent($list, $head, $key)
    {
        $thead = $this->tableHead($head);
        $tbody = $this->tableBody($list, $key);
        $table = '<table style="border: 1px solid black; border-collapse: collapse">'.$thead.$tbody.'</table>';
        return $table;
    }

    /**
     * build table head
     * 
     * @param array $head
     * @return string
     */
    private function tableHead($head)
    {
        $th = '';
        for ($i = 0; $i < count($head); $i++) {
            $th = $th.'<th style="border: 1px solid black;padding: 15px;text-align: left;">'.$head[$i].'</th>';
        }
        $thead = '<thead><tr>'.$th.'</tr></thead>';
        return $thead;
    }

    /**
     * build table body
     * 
     * @param array $list
     * @param array $key
     * @return string
     */
    private function tableBody($list, $key)
    {
        $rows = '';
        for ($i = 0; $i < count($list); $i++) {
            $rows = $rows.$this->tableBodyRow($list[$i], $key);
        }
        $body = '<tbody>'.$rows.'</tbody>';
        return $body;
    }

    /**
     * build table body row
     * 
     * @param array $item
     * @param array $key
     * @return string
     */
    private function tableBodyRow($item, $key)
    {
        $td = '';
        for ($i = 0; $i < count($key); $i++) {
            $td = $td.'<td style="border: 1px solid black;padding: 8 4;text-align: left;">'.$item[$key[$i]].'</td>';
        }
        $tr = '<tr>'.$td.'</tr>';
        return $tr;
    }

    /**
     * send mail handler
     * 
     * @param string $subject
     * @param string $sender
     * @param string $recipient
     * @param string $content
     * @return void
     */
    private function sendMail($subject, $sender, $recipient, $content)
    {
        $nu = null;
        $pdo = DB::getPdo();
        $stmt = $pdo->prepare("begin pk_mail.proc_mail_02(:f, :t1, :t2, :t3, :c1, :c2, :c3, :s, :m); end;");
        $stmt->bindParam(':f', $sender);
        $stmt->bindParam(':t1', $recipient);
        $stmt->bindParam(':t2', $nu);
        $stmt->bindParam(':t3', $nu);
        $stmt->bindParam(':c1', $nu);
        $stmt->bindParam(':c2', $nu);
        $stmt->bindParam(':c3', $nu);
        $stmt->bindParam(':s', $subject);
        $stmt->bindParam(':m', $content);
        $stmt->execute();
    }
}