<?php
/**
 * 系統使用者資料處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/06/22
 * @since 1.0.0 spark: 建立檔案寫入與讀取相關的資料處理
 * 
 */
namespace App\Repositories\Web;

use Exception;
use App\Traits\Sqlexecute;
use App\Repositories\Repository;


/**
 * Class UserRepository
 *
 * @package App\Repositories
 */
class UserRepository extends Repository
{   
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\Models\Web\User';
    }

    public function authUser($id, $password) {
        return $this->model
            ->where('id', $id)
            ->where('pwd', $password)
            ->where('state', 'Y')
            ->first();
    }
}