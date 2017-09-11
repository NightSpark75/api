<?php
/**
 * 檔案上傳與下載資料處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/06/22
 * @since 1.0.0 spark: 建立檔案寫入與讀取相關的資料處理
 * 
 */
namespace App\Repositories\Native;

use DB;
use Exception;
use App\Traits\Sqlexecute;

/**
 * Class PadRepository
 *
 * @package App\Repositories
 */
class PadRepository
{   
    use Sqlexecute;

    public function saveBundle($version, $file)
    {
        try {
            $bindings['version'] = $version;
            $bindings['bundle_file'] = base64_encode(file_get_contents($file));
    
            $query = 
                "insert into api_bundle_version (version, bundle_file, created_at)
                    values (:version, :bundle_file, sysdate)
            ";
            $this->query($bindings, $query);
            return [
                'result' => true,
                'msg' => 'bundle upload success!',
            ];
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    public function saveApk($version, $file)
    {
        try {
            $bindings['version'] = $version;
            $bindings['bundle_file'] = base64_encode(file_get_contents($file));
    
            $query = 
                "insert into api_bundle_version (version, bundle_file, created_at)
                    values (:version, :bundle_file, sysdate)
            ";
            $this->query($bindings, $query);
            return [
                'result' => true,
                'msg' => 'bundle upload success!',
            ];
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    public function downloadBundle()
    {
        try {
            $file = DB::selectOne("
                select bundle_file
                from(select bundle_file, rownum
                    from api_bundle_version
                    where version between 1000000000 and 1999999999
                    order by version desc)
                where rownum = 1
            ")->bundle_file;
            return [
                'result' => true,
                'file' => $file,
            ];
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    public function downloadApk()
    {
        try {
            $file = DB::selectOne("
                select bundle_file
                from(select bundle_file, rownum
                    from api_bundle_version
                    where version between 3000000000 and 3999999999
                    order by version desc)
                where rownum = 1
            ")->bundle_file;
            return [
                'result' => true,
                'file' => $file,
            ];
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    public function getVersion()
    {
        try {
            $ver = DB::selectOne("
                select max(version) version
                from api_bundle_version
                where version between 1000000000 and 1999999999
            ")->version;
            return [
                'result' => true,
                'version' => $ver
            ];
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }
}