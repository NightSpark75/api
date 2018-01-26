<?php
/**
 * JWT帳號驗證處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/01/12
 * @since 1.0.0 spark: 完成登入登出功能
 * 
 */
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Web\JwtService;
use JWTAuth;
use Exception;
use App\Traits\Common;

/**
 * Class JwtController
 *
 * @package App\Http\Controllers
 */
class JwtController extends Controller
{
    use Common;

    private $jwtService;

    /**
     * construct
     * 
     * @param JwtService $auth
     * @return void
     */
    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function login()
    {
        try {
            $id = request()->input('id');
            $password = request()->input('password');
            $token = $this->jwtService->login($id, $password);
            return response()->json(compact('token'), 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    public function refresh()
    {
        try {
            $token = JWTAuth::getToken();
            $token = $this->jwtService->refresh($token);
            return response()->json(compact('token'), 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }
}
