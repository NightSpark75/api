<?php
/**
 * overdue notice service
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/02/23
 * @since 1.0.0 spark: handle point record overdue notice
 * 
 */
namespace App\Services\MPZ;

use App\Repositories\MPZ\PointlogRepository;
use App\Services\CommonService;
use Exception;
use DB;

/**
 * Class OverdueService
 *
 * @package App\Services
 */
class OverdueService {

    /**
     * @var PointlogRepository
     */
    private $pointlogRepository;

    private $common;

    /**
     * @param PointlogRepository $pointlogRepository
     * @param CommonService $common
     */
    public function __construct(
        PointlogRepository $pointlogRepository,
        CommonService $common
    ) {
        $this->pointlogRepository = $pointlogRepository;
        $this->common = $common;
    }

    /**
     * yesterday no record notice at 2200 
     */
    public function notice2200()
    {
        $date = date('Ymd');
        $workDate = $this->common->getWorkDate($date, 1);
        if ($date === $workDate) {
            return $this->noRecordDetail($date);
        }
        return $date.' 非工作日';
    }

    /**
     * send today no record list
     * 
     * @param int $date
     */
    private function noRecordDetail($date)
    {
        $catch = $this->catchDetailHandler($date);
        $temp = $this->tempDetailHandler($date);
        $wetest = $this->wetestDetailHandler($date);
        $refri = $this->refriDetailHandler($date);
        $pressure = $this->pressureDetailHandler($date);
        $list = array_collapse([$catch, $temp, $wetest, $refri, $pressure]);
        return $this->sendNoRecordDetail($list);
    }

    private function catchDetailHandler($date)
    {   
        $mo = "case when ldate is null then 'X' else '' end mo";
        $af = "'' af";
        $ev = "'' ev";
        $catch = $this->getDetail('mpz_catchlog', $date, 'C', $mo, $af, $ev);
        return $catch;
    }

    private function getDetail($table, $date, $type, $mo, $af, $ev) {
        $arr = [];
        $list = $this->pointlogRepository->noRecordDetail($table, $date, $type, $mo, $af, $ev);
        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]->mo === 'X' || $list[$i]->af === 'X'|| $list[$i]->ev === 'X') {
                array_push($arr, $list[$i]);
            }
        }
        return $arr;
    }

    private function tempDetailHandler($date)
    {
        $mo = "case when mo_user is null then 'X' else '' end mo";
        $af = "case when af_user is null then 'X' else '' end af";
        $ev = "case when ev_user is null then 'X' else '' end ev";
        $temp = $this->getDetail('mpz_templog', $date, 'T', $mo, $af, $ev);
        return $temp;
    }

    private function wetestDetailHandler($date)
    {
        $mo = "case when mo_user is null then 'X' else '' end mo";
        $af = "case when af_user is null then 'X' else '' end af";
        $ev = "case when ev_user is null then 'X' else '' end ev";
        $wetest = $this->getDetail('mpz_wetestlog', $date, 'W', $mo, $af, $ev);
        return $wetest;
    }

    private function refriDetailHandler($date)
    {
        $mo = "case when mo_user is null then 'X' else '' end mo";
        $af = "case when af_user is null then 'X' else '' end af";
        $ev = "''ev";
        $refri = $this->getDetail('mpz_refrilog', $date, 'R', $mo, $af, $ev);
        return $refri;
    }

    private function pressureDetailHandler($date)
    {;
        $mo = "case when mo_user is null then 'X' else '' end mo";
        $af = "case when af_user is null then 'X' else '' end af";
        $ev = "case when ev_user is null then 'X' else '' end ev";
        $pressure = $this->getDetail('mpz_pressurelog', $date, 'P', $mo, $af, $ev);
        return $pressure;
    }


    private function sendNoRecordDetail($list)
    {
        $date = date('Ymd');
        $content = $this->setDetailMailContent($list);
        $count = count($list);
        $subject = $date.'未記錄詳細清單!';
        $sender = 'mpz.system@standard.com.tw';
        $t1 = 'Lin.Guanwei@standard.com.tw';
        $t2 = 'Chen.Ian@standard.com.tw';
        $t3 = 'Kuo.Hung@standard.com.tw';
        try {
            $this->common->sendMail($subject, $sender, $t1, $t2, $t3, $content);
        } catch (Exception $e) {
            $content = "因筆數太多( $count 筆)，暫不顯示清單";
            $this->common->sendMail($subject, $sender, $t1, $t2, $t3, $content);
        }
        return $subject.', count='.$count;
    }

    /**
     * today no record notice at 0830
     */
    public function notice0830()
    {
        $date = date('Ymd');
        $workDate = $this->common->getWorkDate($date, 1);
        if ($date === $workDate) {
            return $this->noRecord0830($date);
        }
        return $date.' 非工作日';
    }

    /**
     * send no record list by date
     * 
     * @param int $date
     */
    private function noRecord0830($date)
    {
        $list = $this->pointlogRepository->noRecord($date);
        $content = $this->setMailContent($list);
        $subject = $date.' 0830未記錄清單!';
        $sender = 'mpz.system@standard.com.tw';
        $t1 = 'Lin.Guanwei@standard.com.tw';
        $t2 = 'Chen.Ian@standard.com.tw';
        $t3 = 'Kuo.Hung@standard.com.tw';
        $this->common->sendMail($subject, $sender, $t1, $t2, $t3, $content);
        return $subject;
    }

    /**
     * today no record notice at 1330
     */
    public function notice1330()
    {
        $date = date('Ymd');
        $workDate = $this->common->getWorkDate($date, 1);
        if ($date === $workDate) {
            return $this->noRecord1330($date);
        }
        return $date.' 非工作日';
    }

    /**
     * send no record list at 1330
     * 
     * @param int $date
     */
    private function noRecord1330($date)
    {
        $temp = $this->pointlogRepository->noRecordByType('mpz_templog', 'T', 'af', $date);
        $wetest = $this->pointlogRepository->noRecordByType('mpz_wetestlog', 'W', 'af', $date);
        $pressure = $this->pointlogRepository->noRecordByType('mpz_pressurelog', 'P', 'af', $date);
        $list = array_collapse([$temp, $wetest, $pressure]);
        $content = $this->setMailContent($list);
        $subject = $date.' 1330未記錄清單!';
        $sender = 'mpz.system@standard.com.tw';
        $t1 = 'Lin.Guanwei@standard.com.tw';
        $t2 = 'Chen.Ian@standard.com.tw';
        $t3 = 'Kuo.Hung@standard.com.tw';
        $this->common->sendMail($subject, $sender, $t1, $t2, $t3, $content);
        return $subject;
    }

    /**
     * today no record notice at 1700
     */
    public function notice1700()
    {
        $date = date('Ymd');
        $workDate = $this->common->getWorkDate($date, 1);
        if ($date === $workDate) {
            return $this->noRecord1700($date);
        }
        return $date.' 非工作日';
    }

    /**
     * send no record list at 1700
     * 
     * @param int $date
     */
    private function noRecord1700($date)
    {
        $temp = $this->pointlogRepository->noRecordByType('mpz_templog', 'T', 'ev', $date);
        $wetest = $this->pointlogRepository->noRecordByType('mpz_wetestlog', 'W', 'ev', $date);
        $refri = $this->pointlogRepository->noRecordByType('mpz_refrilog', 'R', 'af', $date);
        $pressure = $this->pointlogRepository->noRecordByType('mpz_pressurelog', 'P', 'ev', $date);
        $list = array_collapse([$temp, $wetest, $refri, $pressure]);
        $content = $this->setMailContent($list);
        $subject = $date.' 1700未記錄清單!';
        $sender = 'mpz.system@standard.com.tw';
        $t1 = 'Lin.Guanwei@standard.com.tw';
        $t2 = 'Chen.Ian@standard.com.tw';
        $t3 = 'Kuo.Hung@standard.com.tw';
        $this->common->sendMail($subject, $sender, $t1, $t2, $t3, $content);
        return $subject;
    }

    /**
     * handler set mail content 
     * 
     * @param array $data
     */
    private function setMailContent($data)
    {
        $tr = '';
        for ($i = 0; $i < count($data); $i++) {
            $td1 = $this->setTd($data[$i]->point_no);
            $td2 = $this->setTd($data[$i]->point_name);
            $td3 = $this->setTd($data[$i]->point_des);
            $tr = $tr."<tr>$td1$td2$td3</tr>";
        }
        $thead = "<thead><tr><td>編號</td><td>名稱</td><td>區域</td></tr></thead>";
        $tbody = "<tbody>$tr<tbody>";
        $table = "<table style=\"border: 1px solid black; border-collapse: collapse\">$thead$tbody</table>";
        return $table;
    }

    /**
     * return table td 
     * 
     * @param string $str
     */
    private function setTd($str)
    {
        return "<td style=\"border: 1px solid black;padding: 8 4;text-align: left;\">$str</td>";
    }

    /**
     * handler set detail mail content 
     * 
     * @param array $data
     */
    private function setDetailMailContent($data)
    {
        $tr = '';
        for ($i = 0; $i < count($data); $i++) {
            $td1 = $this->setTd($data[$i]->point_no);
            $td2 = $this->setTd($data[$i]->point_name);
            $td3 = $this->setTd($data[$i]->point_des);
            $td4 = $this->setTd($data[$i]->mo);
            $td5 = $this->setTd($data[$i]->af);
            $td6 = $this->setTd($data[$i]->ev);
            $tr = $tr."<tr>$td1$td2$td3$td4$td5$td6</tr>";
        }
        $thead = "<thead><tr><td>編號</td><td>名稱</td><td>區域</td><td>早上</td><td>下午1</td><td>下午2</td></tr></thead>";
        $tbody = "<tbody>$tr<tbody>";
        $table = "<table style=\"border: 1px solid black; border-collapse: collapse\">$thead$tbody</table>";
        return $table;
    }
    

    /**
     * 
     */
    private function expired()
    {
        //temp

        //wetest

        //refri

        //pressure
    }
}