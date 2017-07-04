<?php
/**
 * 帳號驗證處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/06/29
 * @since 1.0.0 spark: 完成登入登出功能
 * 
 */
namespace App\Http\Controllers;

use App\Repositories\AuthRepository;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    //
    private $auth;

    public function __construct(AuthRepository $auth)
    {
        $this->auth = $auth;
    }


    public function login()
    {
        $account = request()->input('account');
        $password = strtoupper(request()->input('password'));
        $system = request()->input('system');
        $result = $this->auth->login($account, $password, $system);
        $response = response()->json($result);
        return $response;
    }

    public function logout()
    {
        $this->auth->logout();
        $response = redirect()->route('thanks');
        $c = \Auth::check();
        $a = \Auth::user();
        return $response;
    }

    public function getUser()
    {
        $a = \Auth::user();
        return $a;
    }
}
