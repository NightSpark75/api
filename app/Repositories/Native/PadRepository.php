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
            $bindings['file_size'] = $file->getClientSize();
    
            $query = 
                "insert into api_bundle_version (version, bundle_file, created_at, file_size)
                    values (:version, :bundle_file, sysdate, :file_size)
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
            $bindings['file_size'] = $file->getClientSize();
    
            $query = 
                "insert into api_bundle_version (version, bundle_file, created_at, file_size)
                    values (:version, :bundle_file, sysdate, :file_size)
            ";
            $this->query($bindings, $query);
            return [
                'result' => true,
                'msg' => 'apk upload success!',
            ];
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    public function downloadBundle($app)
    {
        try {
            $file = DB::selectOne("
                select bundle_file
                from(select bundle_file, rownum
                    from api_bundle_version
                    where substr(version, 1, 3) = to_char($app)
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

    public function downloadApk($app)
    {
        try {
            $file = DB::selectOne("
                select bundle_file, version
                    from(
                        select bundle_file, version
                            from api_bundle_version
                            where substr(version, 1, 3) = to_char($app)
                            order by version desc
                        )
                    where rownum = 1

                select bundle_file, version
                    from api_bundle_version
                    where substr(version, 1, 3) = to_char($app) 
            ");
            return [
                'result' => true,
                'file' => $file->bundle_file,
                'version' => $file->version,
            ];
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    public function getVersion($app)
    {
        try {
            $version_info = DB::selectOne("
                select version_number, file_size
                    from(
                        select version version_number, file_size, rownum
                            from api_bundle_version
                            where substr(version, 1, 3) = to_char($app)    
                            order by version desc
                    )
                    where rownum = 1     
            ");
            $version =  (int)substr($version_info->version_number, 3, 3) . '.' .
                        (int)substr($version_info->version_number, 6, 2) . '.' .
                        (int)substr($version_info->version_number, 8, 2);
            $app = substr($version_info->version_number, 0, 3);
            return [
                'result' => true,
                'app' => $app,
                'version' => $version,
                'version_number' => $version_info->version_number,
                'size' => $version_info->file_size
            ];
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }
}