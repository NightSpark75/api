<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Web\UserRepository;

class UserController extends Controller
{
    //
    private $user;
    private $program;

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
        $this->program = 'SMAF0030';
        session(['program' => $this->program]);
        $this->middleware('role');
    }

    public function init()
    {
        $user_list = $this->user->getUser();
        $user_id = Auth()->user()->id;
        $prg_id = $this->program;
        $prg = session('program');
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
        $this->middleware('role:insert');
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
        $this->middleware('role:update');
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
        $this->middleware('role:delete');
        $r = request();
        $input = request()->all();
        $user_id = $input['user_id'];
        $result = $this->user->delete($user_id);
        $response = response()->json($result);
        return $response;
    }

    public function search($str = '')
    {
        $result = $this->user->search($str);
        $response = response()->json($result);
        return $response;
    }
}
