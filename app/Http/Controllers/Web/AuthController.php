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
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\Web\AuthRepository;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    //
    private $auth;

    /**
     * construct
     * 
     * @param AuthRepository $auth
     * @return void
     */
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
        $system = session('system');
        $this->auth->logout();
        return redirect('/web/login/'.$system);
    }

    public function menu()
    {
        if (auth()->check()) {
            $user_id = auth()->user()->id;
            $result = $this->auth->getMenu($user_id);
            $response = response()->json($result);
            return $response;
        }
        $result = ['result' => false, 'msg' => '尚未登入，無法取得功能清單!(#0001)'];
        $response = response()->json($result);
        return $response;
    }

    public function commonMenu($class)
    {
        $result = $this->auth->getCommonMenu($class);
        $response = response()->json($result);
        return $response;
    }

    public function user()
    {
        $info = $this->auth->getUser();
        return response()->json($info);
    }
}
