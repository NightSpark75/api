<?php

namespace App\Http\Controllers\MPZ;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\MPZ\CatchlogRepository;

class CatchlogController extends Controller
{
    //
    private $catchlog;

    public function __construct(CatchlogRepository $catchlog)
    {
        $this->catchlog = $catchlog;
        $this->program = 'MPZW0010';
        session(['program' => $this->program]);
        //$this->middleware('role');
    }

    public function save()
    {
        $this->middleware('role:insert');
        $params = request()->all();
        $result = $this->catchlog->save($params);
        $response = response()->json($result);
        return $response;
    }

    public function init($point_no)
    {
        $result = $this->catchlog->init($point_no);
        $response = response()->json($result);
        return $response;
    }
}
