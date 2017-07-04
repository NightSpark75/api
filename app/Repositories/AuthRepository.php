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
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthRepository
 *
 * @package App\Repositories
 */
class AuthRepository
{   
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * 使用者登入
     * 
     * @param string $account
     * @param string $password
     * @param string $system
     * @return array
     */
    public function login($account, $password, $system)
    {
        try {
            $auth = 
                $this->user
                    ->where('id', $account)
                    ->where('pwd', $password)
                    ->where('sys', $system)
                    ->first();
            if ($auth) {
                Auth::login($auth);
                return ['result' => true, 'msg' => '登入成功!(#0000)'];
            }
            throw new Exception('帳號或密碼錯誤!(#0001)');
        } catch (Exception $e) {
            return ['result' => false, 'msg' => $e->getMessage()];
        }
    }


    /**
     * 使用者登出
     * 
     * @return Response
     */
    public function logout()
    {
        Auth::logout();
    }

}