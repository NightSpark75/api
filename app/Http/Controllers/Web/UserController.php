<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\UserRepository;

class UserController extends Controller
{
    //
    private $user;

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    public function init()
    {
        if (auth()->check() == false) {
            $data = [
                'result' => false
            ];
            $response = response()->json($data);
            return $response;
        }
        $user_list = $this->user->getUser();
        $user_id = Auth()->user()->id;
        $prg_id = 'SMAF0030';
        $prg = $this->user->getPrg($user_id, $prg_id);
        $data = [
            'result' => true,
            'user' => $user_list,
            'prg' => $prg,
        ];
        $response = response()->json($data);
        return $response;
    }

    public function insert()
    {
        $input = request()->all();
        $params = [
            'co' => 'C010',
            'user_id' => $input['user_id'],
            'user_name' => $input['user_name'],
            'user_pw' => $input['user_pw'],
            'pw_ctrl' => $input['pw_ctrl'],
            'rmk' => $input['rmk'],
            'duser' => auth()->user()->id,
            'class' => $input['class'],
            'state' => $input['state'],
        ];
        $result = $this->user->insert($params);
        $response = response()->json($result);
        return $response;
    }

    public function update()
    {
        $input = request()->all();
        $user_id = $input['user_id'];
        $params = [
            'user_name' => $input['user_name'],
            'user_pw' => $input['user_pw'],
            'pw_ctrl' => $input['pw_ctrl'],
            'rmk' => $input['rmk'],
            'duser' => auth()->user()->id,
            'class' => $input['class'],
            'state' => $input['state'],
        ];
        $result = $this->user->update($params, $user_id);
        $response = response()->json($result);
        return $response;
    }

    public function delete()
    {
        $r = request();
        $input = request()->all();
        $user_id = $input['user_id'];
        $result = $this->user->delete($user_id);
        $response = response()->json($result);
        return $response;
    }

    public function search($str)
    {
        $result = $this->user->search($str);
        $response = response()->json($result);
        return $response;
    }
}
