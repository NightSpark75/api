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
        return $response;
    }

    public function menu()
    {
        $a = auth();
        $u = auth()->user();
        $result = ['result' => false, 'msg' => '尚未登入，無法取得功能清單!(#0001)'];
        if (auth()->check()) {
            $user_id = auth()->user()->id;
            $result = $this->auth->getMenu($user_id);
        }
        $response = response()->json($result);
        return $response;
    }
}
