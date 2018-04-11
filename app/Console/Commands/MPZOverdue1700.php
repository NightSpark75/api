<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;
use App\Services\MPZ\OverdueService;

class MPZOverdue1700 extends Command
{
    private $service;

    // 命令名稱
    protected $signature = 'Web:MPZ.Overdue1700';

    // 說明文字
    protected $description = '[通知] 倉管監控點未記錄通知';

    public function __construct(OverdueService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    // Console 執行的程式
    public function handle()
    {
        $result = $this->service->notice1700();

        // 檔案紀錄在 storage/test.log
        $log_file_path = storage_path('/logs/MPZOverdue.log');

        // 記錄 JSON 字串
        $log_info_json = date('Y-m-d H:i:s') . ', ' . $result . "\r\n";

        // 記錄 Log
        File::append($log_file_path, $log_info_json);
    }
}