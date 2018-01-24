<?php
/**
 * 揀貨資料處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/01/22
 * @since 1.0.0 spark: build database I/O function 
 * 
 */
namespace App\Repositories\Native;

use DB;
use Exception;
use App\Traits\Sqlexecute;

/**
 * Class PickingRepository
 *
 * @package App\Repositories
 */
class PickingRepository
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
            if ((int)substr($app, 0, 1) != 2) {
                throw new Exception('invalid bundle number');
            }
            $file = DB::selectOne("
                select bundle_file
                from(select bundle_file, rownum
                    from api_bundle_version
                    where substr(version, 1, 3) = to_char($app)
                    order by version desc)
                where rownum = 1
            ")->bundle_file;

            if (!isset($file)) {
                throw new Exception('bundle not found');
            }

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
            if ((int)substr($app, 0, 1) != 1) {
                throw new Exception('invalid apk number');
            }
            $file = DB::selectOne("
                select apk_file, version
                    from(
                        select bundle_file apk_file, version
                            from api_bundle_version
                            where substr(version, 1, 3) = to_char($app)
                            order by version desc
                        )
                    where rownum = 1
            ");

            if (!isset($file)) {
                throw new Exception('apk not found');
            }

            return [
                'result' => true,
                'file' => $file->apk_file,
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