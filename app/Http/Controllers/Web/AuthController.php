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
use JWTAuth;
use JWTException;
use App\Models\Web\User;

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

    public function password()
    {   
        $params = request()->input();
        $result = $this->auth->password($params);
        $response = response()->json($result);
        return $response;
    }

    public function nativeLogin()
    {
        $account = request()->input('account');
        $password = strtoupper(request()->input('password'));
        $system = request()->input('system');
        $result = $this->auth->nativeLogin($account, $password, $system);
        $response = response()->json($result);
        return $response;
    }

    public function logout()
    {
        $system = session('system')? session('system'): 'ppm';
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

    public function checkLogin()
    {
        if (auth()->check()) {
            return response()->json(['result' => true]);
        } else {
            return response()->json(['result' => false]);
        }
    }
}
