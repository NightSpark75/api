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
        $this->program = 'SMAF0030';
        session(['program' => $this->program]);
        $this->middleware('role');
    }

    public function save()
    {
        $this->middleware('role:insert');
        $params = request()->all();
        $result = $this->catchlog->save($params);
        $response = response()->json($result);
        return $response;
    }

    public function catchCount($point_no, $ldate)
    {
        $result = $this->catchlog->catchCount($point_no, $ldate);
        $response = response()->json($result);
        return $response;
    }
}
