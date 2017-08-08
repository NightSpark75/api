<?php
/**
 * QC文件資料處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/08/08
 * @since 1.0.0 spark: 建立檔案寫入與讀取相關的資料處理
 * 
 */
namespace App\Repositories\MPE\QC;

use DB;
use Exception;
use App\Traits\Sqlexecute;

/**
 * Class DocumentRepository
 *
 * @package App\Repositories\MPE\QC
 */
class DocumentRepository
{   
    use Sqlexecute;
    
    public function __construct() {

    }

    public function getDownloadUrl($file_id)
    {     
        $file = DB::selectOne("
            select *
            from api_file_code
            where file_id = '$file_id'
        ");

        $decode = base64_decode($file->code);
        $code = $file->code;
        $name = mb_convert_encoding($file->name,"BIG-5","UTF-8");
        $mime = $file->mime;
        $extension = $file->extension;

        // 建構標頭
        $response = 
            response($decode)
            ->header('Content-Type', $mime) // MIME
            ->header('Content-length', strlen($code)) // base64
            ->header('Content-Transfer-Encoding', 'binary');
        return $response;
    }
}   