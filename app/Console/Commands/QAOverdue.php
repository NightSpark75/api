<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;
use App\Services\MPE\QA\OverdueService;

class QAOverdue extends Command
{
    private $service;

    // 命令名稱
    protected $signature = 'Web:QA:Overdue';

    // 說明文字
    protected $description = '[通知] 留樣品領用逾期未歸還通知';

    public function __construct(OverdueService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    // Console 執行的程式
    public function handle()
    {
        $result = $this->service->overdueNotice();

        // 檔案紀錄在 storage/test.log
        $log_file_path = storage_path('/logs/QAOverdue.log');

        // 記錄 JSON 字串
        $log_info_json = date('Y-m-d H:i:s') . ', ' . $result . "\r\n";

        // 記錄 Log
        File::append($log_file_path, $log_info_json);
    }
}