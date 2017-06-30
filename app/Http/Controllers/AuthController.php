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
        $password = request()->input('password');
        $system = request()->input('system');

        $response = response();
        return $response;
    }

    public function logout()
    {
        
    }
}
