<?php

namespace App\Services\Web;

use JWTAuth;
use Exception;
use App\Traits\Sqlexecute;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\Web\UserRepository;
use App\Repositories\Web\ApiLoginLogRepository;

class JwtService {

    use Sqlexecute;

    private $userRepository;
    private $apiLoginLogRepository;

    public function __construct(
        UserRepository $userRepository,
        ApiLoginLogRepository $apiLoginLogRepository
    ) {
        $this->userRepository = $userRepository;
        $this->apiLoginLogRepository = $apiLoginLogRepository;
    }

    public function login($id, $password)
    {
        try {
            // attempt to verify the credentials and create a token for the user
            $user = $this->userRepository->authUser($id, $password);
            if (!$user) {
                throw new Exception('帳號或密碼錯誤!');
            }
            $payload = ['name' => $user->name];
            if (!$token = JWTAuth::fromUser($user, $payload)) {
                throw new Exception('Token建立失敗!');
            }
            return $token;
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            throw new Exception('伺服器錯誤:無法建立Token('.$e->getMessage().')');
        }
    }

    public function refresh($token)
    {
        try {
            if ($user = JWTAuth::toUser($token)) {
                $refresh = JWTAuth::refresh($token);
                return $refresh;
            }
            throw new Exception('不合法的Token');
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            throw new Exception('伺服器錯誤:無法建立Token('.$e->getMessage().')');
        }
    }
}