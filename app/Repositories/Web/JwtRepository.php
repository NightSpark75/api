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

use JWTAuth;
use JWTException;
use Exception;
use App\Traits\Sqlexecute;
use App\Models\Web\User;
use App\Models\Web\UserPrg;
use Illuminate\Support\Facades\DB;

/**
 * Class JwtRepository
 *
 * @package App\Repositories
 */
class JwtRepository
{   
    use Sqlexecute;
    private $user;
    private $prg;

    public function __construct(User $user, UserPrg $prg)
    {
        $this->user = $user;
        $this->prg = $prg;
    }

    public function login($input)
    {
        $user = $this->getUser($input);
        try {
            // attempt to verify the credentials and create a token for the user
            if (!$user) {
                $code = 401;
                $error = 'invalid_credentials';
            } else {
                $payload = ['name' => $user->name];
                if (!$token = JWTAuth::fromUser($user, $payload)) {
                    $code = 401;
                    $error = 'invalid_credentials';
                } else {
                    $code = 200;
                }
            } 
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            $code = 500;
            $error = 'could_not_create_token';
        }
        return response()->json(compact('token', 'error'), $code);
    }

    public function refresh($input)
    {
        $user = JWTAuth::toUser($input['token']);
        if ($user) {
            $token = JWTAuth::refresh($input['token']);
            return response()->json(compact('token'));
        }
    }

    public function toUser($input)
    {
        $user = JWTAuth::toUser($input['token']);
        if ($user->id = $input['id']) {
            $code = 200;
        }
        return response()->json(compact('user', 'error'), $code);
    }

    private function getUser($input)
    {
        $user = $this->user
            ->where('id', $input['id'])
            ->where('pwd', strtoupper($input['password']))
            ->where('state', 'Y')
            ->first();
        return $user;
    }
    
}