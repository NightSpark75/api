<?php

namespace App\Services\Web;

use Excel;
use Exception;
use DB;

class ExcelService {

    public function export($fileName, $sheetName, $head, $content, $type='xlsx'){
        $rows = array_collapse([$head, $content]);
        Excel::create($fileName, function($excel) use ($sheetName, $rows) {
            $excel->sheet($sheetName, function($sheet) use ($rows) {
                $sheet->rows($rows);
            });
        })->export($type);
    }

    public function test()
    {
        //Excel文件导出功能 By Laravel学院
        $cellData = [
            ['学号','姓名','成绩'],
            ['10001','AAAAA','99'],
            ['10002','BBBBB','92'],
            ['10003','CCCCC','95'],
            ['10004','DDDDD','89'],
            ['10005','EEEEE','96'],
        ];
        return Excel::download($cellData, 'test.xlsx');
    }
}