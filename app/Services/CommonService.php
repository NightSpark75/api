<?php
/**
 * 共用元件函式庫
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/06/22
 * @since 1.0.0 spark: 完成檔案上傳與下載功能
 * 
 */
namespace App\Services;
use DB;

/**
 * Class CommonService
 *
 * @package App\Service
 */
class CommonService
{
    /**
     * 取得UUID
     *
     * @return void
     */
    function getUuid() 
    {
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);  // "-"
        $uuid = ''.substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        return $uuid;
    }

    /**
     * send mail handler
     * 
     * @param string $subject
     * @param string $sender
     * @param string $t1
     * @param string $t2
     * @param string $t3
     * @param string $content
     * @return void
     */
    public function sendMail($subject, $sender, $t1, $t2, $t3, $content)
    {
        $nu = null;
        $pdo = DB::getPdo();
        $c1 = 'Lin.Yupin@standard.com.tw';
        $stmt = $pdo->prepare("begin pk_mail.proc_mail_02(:f, :t1, :t2, :t3, :c1, :c2, :c3, :s, :m); end;");
        $stmt->bindParam(':f', $sender);
        $stmt->bindParam(':t1', $t1);
        $stmt->bindParam(':t2', $t2);
        $stmt->bindParam(':t3', $t3);
        $stmt->bindParam(':c1', $c1);
        $stmt->bindParam(':c2', $nu);
        $stmt->bindParam(':c3', $nu);
        $stmt->bindParam(':s', $subject);
        $stmt->bindParam(':m', $content);
        $stmt->execute();
    }

    /**
     * get work date
     * 
     * @param int $date
     * @param int $day
     * @return int
     */
    public function getWorkDate($date, $day) {
        $workDate = DB::selectOne("
            select stdadm.pk_hra.fu_work_day('ALL', $date, $day) d from dual
        ")->d;
        return $workDate;
    }
}