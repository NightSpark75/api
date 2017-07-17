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
namespace App\Repositories;

use Exception;
use App\Traits\Sqlexecute;

use App\Models\UserList;
use App\Models\UserPrg;

/**
 * Class UserRepository
 *
 * @package App\Repositories
 */
class UserRepository
{   
    use Sqlexecute;
    
    private $user;
    private $prg;

    public function __construct(UserList $user, UserPrg $prg)
    {
        $this->user = $user;
        $this->prg = $prg;
    }

    public function getUser()
    {
        $result = $this->user
            ->where('state', 'Y')->get()->toArray();
        return $result;
    }

    public function getPrg($user_id, $prg_id)
    {
        $result = $this->prg
            ->where('user_id', $user_id)
            ->where('prg_id', $prg_id)->first();
        return $result;
    }

    public function insert($params)
    {
        try{
            $this->user->insert($params);
            $user = $this->user->where('user_id', $params['user_id'])->first();
            $result = [
                'result' => true,
                'msg' => '新增使用者資料成功(#0001)',
                'user' => $user,
            ];
            return $result;
        } catch (\Exception $e) {
            $result = [
                'result' => false,
                'msg' => $e->getMessage(),
            ];
            return $result;
        }
    }
    
    public function update($params, $user_id)
    {
        try{
            $this->user->where('user_id', $user_id)->update($params);
            $user = $this->user->where('user_id', $user_id)->first();
            $result = [
                'result' => true,
                'msg' => '更新使用者資料成功(#0002)',
                'user' => $user,
            ];
            return $result;
        } catch (\Exception $e) {
            $result = [
                'result' => false,
                'msg' => $e->getMessage(),
            ];
            return $result;
        }
    }

    public function delete($user_id)
    {
        try{
            $this->user->where('user_id', $user_id)->delete();
            $result = [
                'result' => true,
                'msg' => '刪除使用者資料成功(#0003)',
            ];
            return $result;
        } catch (\Exception $e) {
            $result = [
                'result' => false,
                'msg' => $e->getMessage(),
            ];
            return $result;
        }
    }

    public function search($str)
    {
        $search = '%'.$str.'%';
        try{
            $user = $this->user
                ->where('user_id', 'like', $search)
                ->orWhere('user_name', 'like', $search)
                ->orWhere('rmk', 'like', $search)->get()->toArray();
            $result = [
                'result' => true,
                'msg' => '搜尋使用者資料成功(#0004)',
                'user' => $user,
            ];
            return $result;
        } catch (\Exception $e) {
            $result = [
                'result' => false,
                'msg' => $e->getMessage(),
            ];
            return $result;
        }
    }
}