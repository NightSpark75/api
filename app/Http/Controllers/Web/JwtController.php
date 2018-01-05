<?php
/**
 * 帳號驗證處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/01/12
 * @since 1.0.0 spark: 完成登入登出功能
 * 
 */
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\Web\JwtRepository;

/**
 * Class JwtController
 *
 * @package App\Http\Controllers
 */
class JwtController extends Controller
{
    //
    private $jwt;

    /**
     * construct
     * 
     * @param AuthRepository $auth
     * @return void
     */
    public function __construct(JwtRepository $jwt)
    {
        $this->jwt = $jwt;
    }

    public function login()
    {
        $input = request()->all();
        $result = $this->jwt->login($input);
        return $result;
    }

    public function refresh()
    {
        $input = request()->all();
        $result = $this->jwt->refresh($input);
        return $result;
    }
}
