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
use App\Traits\Sqlexecute;
use App\Models\Web\User;
use App\Models\Web\UserPrg;
use Auth;
use DB;


/**
 * Class AuthRepository
 *
 * @package App\Repositories
 */
class AuthRepository
{   
    use Sqlexecute;
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
            return $this->userLogin($account, $password, $system);
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    public function nativeLogin($account, $password, $system)
    {
        try {
            $user_info = $this->userLogin($account, $password, $system);
            $user_menu = $this->getMenu($account);
            return $this->success([
                'user_info' => $user_info['user_info'],
                'user_menu' => $user_menu['menu'],
            ]);
        } catch (Exception $e) {
            return $this->exception($e, ['info' => $account.' pw:'.$password]);
        }
    }

    private function userLogin($account, $password, $system)
    {
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
            return ['result' => true, 'msg' => '登入成功!(#0000)', 'user_info' => $user_info];
        }
        throw new Exception('帳號或密碼錯誤!(#0001)');
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
        $result = [
            'result' => true, 
            'msg' => '已取得清單!(#0000)', 
            'menu' => $menu
        ];
        return $result;
    }

    /**
     * 取得公用程式清單
     * 
     * @param string $class
     * @return Mix
     */
    public function getCommonMenu($class)
    {
        $menu = DB::select("
            select w.co, w.prg_id, w.web_route, w.rmk, w.prg_name
            from api_web_prg w, api_common_prg c
            where w.co = c.co and w.prg_id = c.prg_id and c.cls = '$class'
        ");
        $result = [
            'result' => true, 
            'msg' => '已取得清單!(#0000)', 
            'menu' => $menu
        ];
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

    /**
     * 取得登入者的角色
     * 
     * @return Mix
     */
    
    public function getUserRole($user)
    {
        //$user = auth()->user();
        $binds = [
            'co' => $user->co,
            'user_id' => $user->id,
        ];
        $user_role = DB::select("
            select *
            from sma_user_role_d
            where co = :co and user_id = :user_id
        ", $binds);
        return $user_role;
    }
}