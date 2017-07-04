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
namespace App\Repositories;

use Exception;
use App\Traits\Sqlexecute;

/**
 * Class FileRepository
 *
 * @package App\Repositories
 */
class FileRepository
{   
    use Sqlexecute;

    /**
     * 檔案上傳
     * 
     * @param string $id file id
     * @param string $user user id
     * @param uploadFile $file  檔案物件
     * @param boolean $store_type 以路徑方式儲存
     * @return array
     */
    public function uploadFile($id, $user, $file, $store_type)
    {
        try {
            $created_user = $this->checkUpload($id, $user);
            $this->storeType($id, $file, $created_user, $store_type);
            $this->changeFileStatus($id);
            return ['result' => true, 'msg' => '檔案上傳成功!(#0000)'];
        } catch (Exception $e) {
            return ['result' => false, 'msg' => $e->getMessage()];
        }
    }

    /**
     * 上傳前資料檢核
     * 
     * @param string $id file id
     * @param string $user user id
     * @return string created user id
     */
    private function checkUpload($id, $user)
    {
        $file_base = $this->getFileUser($id);
        $created_user = $file_base->user;
        $status = $file_base->status;
        $user_md5 = $this->userToMD5($created_user);

        if($status == 'S') {
            throw new Exception('檔案已上傳成功，無法重複上傳!(#0001)');
        }

        if ($user_md5 != $user) {
            throw new Exception('檔案驗證資訊有誤，您無權限上傳該檔案!(#0002)');
        }
        return $created_user;
    }

    /**
     * 取得建檔使用者與狀態
     * 
     * @param string $id file id
     * @return array user and status
     */
    private function getFileUser($id)
    {
        $query = "
            select created_by as \"user\", status 
                from api_file_base
                where id = '$id'
            ";
        $result = $this->select($query);
        if (isset($result)) {
            return $result;
        }
        throw new Exception('查詢不到檔案資料!(#0003)');
    }

    /**
     * 使用者id md5加密
     * 
     * @param string $id file id
     * @return string
     */
    private function userToMD5($user)
    {
        $query = "select pk_common.get_md5('$user') as \"md5\" from dual";
        $result = $this->select($query);
        return $result->md5;
    }

    /**
     * 依儲存方式進行存檔
     * 
     * @param string $id file id
     * @param uploadFile $file 檔案內容
     * @param string $created_user user id
     * @param string $store_type 儲存方式
     * @return string
     */
    private function storeType($id, $file, $created_user, $store_type)
    {
        if ($store_type == 'path') {
            $this->copyFile($id, $file, $created_user);
            return;
        }
        $this->insertFile($id, $file, $created_user);
    }

    /**
     * 複製檔案到伺服器
     * 
     * @param string $id file id
     * @param uploadFile $file 檔案內容
     * @param string $created_user user id
     * @return array
     */
    private function copyFile($id, $file, $created_user)
    {
        $transform = strtoupper(md5(uniqid(mt_rand(), true)));
        $file_path = storage_path().'\app\public';
        $tmp_name = $transform.'.tmp';

        $bindings['name'] = $file->getClientOriginalName();
        $bindings['extension'] = $file->getClientOriginalExtension();
        $bindings['mime'] = $file->getMimeType();
        $bindings['path'] = $file_path;
        $bindings['transform'] = $tmp_name;
        $bindings['updated_by'] = $created_user;
        $bindings['file_id'] = $id;

        $file->move($file_path, $tmp_name);

        $query = 
            "update api_file_code 
                set name = :name, extension = :extension, mime = :mime, transform = :transform, path = :path,
                    store_type = 'P', updated_by = :updated_by, updated_at = CURRENT_TIMESTAMP
                where file_id = :file_id
        ";
        $this->query($bindings, $query);       
    }

    /**
     * 寫入檔案資料
     * 
     * @param string $id file id
     * @param uploadFile $file 檔案內容
     * @param string $created_user user id
     * @return array
     */
    private function insertFile($id, $file, $created_user)
    {
        $bindings['name'] = $file->getClientOriginalName();
        $bindings['extension'] = $file->getClientOriginalExtension();
        $bindings['mime'] = $file->getMimeType();
        $bindings['code'] = base64_encode(file_get_contents($file));
        $bindings['updated_by'] = $created_user;
        $bindings['file_id'] = $id;
        
        $query = 
            "update api_file_code 
                set name = :name, extension = :extension, mime = :mime, code = :code, 
                    store_type = 'C', updated_by = :updated_by, updated_at = CURRENT_TIMESTAMP
                where file_id = :file_id
        ";
        $this->query($bindings, $query);       
    }

    /**
     * 更新檔案資料狀態
     * 
     * @param string $id file id
     * @return array
     */
    private function changeFileStatus($id)
    {
        $bindings = [$id];
        $query = "
            update api_file_base 
                set status = 'S', updated_by = created_by , updated_at = CURRENT_TIMESTAMP 
                where id = ?
        ";
        $this->query($bindings, $query);
    }

    /**
     * 下載檔案
     * 
     * @param string $token file token
     * @param string $file_id file id
     * @param string $user user id
     * @return mix
     */
    public function downloadFile($token, $file_id, $user)
    {
        try {
            $file_info = $this->getFileInfo($token, $file_id, $user);
            $this->updateFileStatus($token);
            return ['result' => true, 'msg' => '檔案資料截取成功!(#0005)', 'file' => $file_info];
        } catch (Exception $e) {
            return ['result' => false, 'msg' => $e->getMessage()];
        }
    }

    /**
     * 取得檔案資料
     * 
     * @param string $token file token
     * @param string $file_id file id
     * @param string $user user id
     * @return array
     */
    private function getFileInfo($token, $file_id, $user)
    {
        $query = "
            select t.file_id, t.load_user, t.status, c.name, c.extension, c.mime, c.code, 
                    c.path, c.transform, c.store_type, t.created_by
                from api_file_token t, api_file_code c
                where t.file_id = pk_common.get_md5(c.file_id) and t.status = 'G'
                    and t.file_token = '$token' and t.file_id = '$file_id' and t.load_user = '$user'
        ";
        $file_info = $this->select($query);

        if ($file_info == null) {
            throw new Exception('讀取檔案的驗證參數有異常，您無權限讀取此檔!(#0006)');
        }
        return $file_info;
    }

    /**
     * 更新檔案資訊
     * 
     * @param string $token file token
     * @return void
     */
    private function updateFileStatus($token)
    {
        $binds = [$token];
        $update = "
            update api_file_token
                set status = 'L', updated_by = created_by, updated_at = CURRENT_TIMESTAMP
                where file_token = ?
        ";
        $this->query($binds, $update);
    }
}