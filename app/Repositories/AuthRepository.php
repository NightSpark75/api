<?php
/**
 * 帳號驗證相關資料處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/06/29
 * @since 1.0.0 spark: 帳號驗證相關的資料處理
 * 
 */
namespace App\Repositories;

use Exception;
use App\Traits\Sqlexecute;

/**
 * Class AuthRepository
 *
 * @package App\Repositories
 */
class AuthRepository
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
            return ['result' => true, 'msg' => '#0000;檔案上傳成功!'];
        } catch (Exception $e) {
            return ['result' => false, 'msg' => $e->getMessage()];
        }
    }

}