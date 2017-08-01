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
namespace App\Repositories\Web;

use Exception;
use App\Models\Web\User;
use App\Models\Web\UserPrg;
use Auth;

/**
 * Class AuthRepository
 *
 * @package App\Repositories
 */
class AuthRepository
{   
    private $user;
    private $prg;

    public function __construct(User $user, UserPrg $prg)
    {
        $this->user = $user;
        $this->prg = $prg;
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
            if (auth()->check()) {
                return ['result' => true, 'msg' => '登入成功!(#0000)'];
            }
            $auth = 
                $this->user
                    ->where('id', $account)
                    ->where('pwd', $password)
                    ->where('sys', $system)
                    ->where('state', 'Y')
                    ->first();
            if ($auth) {
                Auth::login($auth);
                $user_info = [
                    'system' => $system,
                    'sys' => $auth->sys,
                    'co' => $auth->co,
                    'user_id' => $auth->id,
                    'user_name' => $auth->name,
                ];
                session([
                    'user_info' => $user_info,
                    'system' => $system,
                ]);
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

    /**
     * 取得使用者清單
     * 
     * @param string $user_id
     * @return Mix
     */
    public function getMenu($user_id)
    {
        $menu = $this->prg->where('user_id', $user_id)->get()->toArray();
        $result = ['result' => true, 'msg' => '已取得清單!(#0000)', 'menu' => $menu];
        return $result;
    }

    /**
     * 取得登入使用者資料
     * 
     * @return App\Models\User
     */
    public function getUser()
    {
        if ((session('user_info') === null) || !session()->has('user_info')) {
            if (!auth()->check()) {
                return ['session' => false];
            }
            $auth = auth()->user();
            $user_info = [
                'system' => 'ppm',
                'sys' => $auth->sys,
                'co' => $auth->co,
                'user_id' => $auth->id,
                'user_name' => $auth->name,
            ];
            session(['user_info' => $user_info]);
        }
        $response = [
            'session' => true,
            'info' => session('user_info'),
        ];
        return $response;
    }
}