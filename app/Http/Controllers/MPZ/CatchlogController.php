<?php

namespace App\Http\Controllers\MPZ;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\MPZ\CatchlogRepository;

class CatchlogController extends Controller
{
    //
    private $catchlog;
    private $program;

    public function __construct(CatchlogRepository $catchlog)
    {
        $this->catchlog = $catchlog;
        $this->program = 'SMAF0030';
        session(['program' => $this->program]);
        $this->middleware('role');
    }

    public function init()
    {
        $result = $this->catchlog->init();
        $response = response()->json($result);
        return $response;
    }

    public function check()
    {
        $input = request()->all();
        $result = $this->catchlog->check();
        $response = response()->json($result);
        return $response;
    }

    public function insert()
    {
        $this->middleware('role:insert');
        $params = request()->all();
        $result = $this->catchlog->insert($params);
        $response = response()->json($result);
        return $response;
    }
}
