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
            $this->noRecordDetail($date);
        }
        return '';
    }

    /**
     * send today no record list
     * 
     * @param int $date
     */
    private function noRecordDetail($date)
    {
        $allPoint = $this->pointlogRepository->getPoint();
        for ($i = 0; $i < count($allPoint); $i++) {
            
        }
    }

    private function catchDetailHandler($date)
    {

    }

    private function sendNoRecordDetail($list)
    {
        $content = $this->setMailContent($list);
        $subject = $date.'未記錄詳細清單!';
        $sender = 'mpz.system@standard.com.tw';
        //$recipient = 'Lin.Guanwei@standard.com.tw';
        $recipient = 'Lin.Yupin@standard.com.tw';
        $this->common->sendMail($subject, $sender, $recipient, $content);
    }

    /**
     * today no record notice at 0830
     */
    public function notice0830()
    {
        $date = date('Ymd');
        $workDate = $this->common->getWorkDate($date, 1);
        if ($date === $workDate) {
            $this->noRecord0830($date);
        }
        return '';
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
        //$recipient = 'Lin.Guanwei@standard.com.tw';
        $recipient = 'Lin.Yupin@standard.com.tw';
        $this->common->sendMail($subject, $sender, $recipient, $content);
    }

    /**
     * today no record notice at 1330
     */
    public function notice1330()
    {
        $date = date('Ymd');
        $workDate = $this->common->getWorkDate($date, 1);
        if ($date === $workDate) {
            $this->noRecord1330($date);
        }
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
        //$recipient = 'Lin.Guanwei@standard.com.tw';
        $recipient = 'Lin.Yupin@standard.com.tw';
        $this->common->sendMail($subject, $sender, $recipient, $content);
    }

    /**
     * today no record notice at 1700
     */
    public function notice1700()
    {
        $date = date('Ymd');
        $workDate = $this->common->getWorkDate($date, 1);
        if ($date === $workDate) {
            $this->noRecord1700($date);
        }
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
        //$recipient = 'Lin.Guanwei@standard.com.tw';
        $recipient = 'Lin.Yupin@standard.com.tw';
        $this->common->sendMail($subject, $sender, $recipient, $content);
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