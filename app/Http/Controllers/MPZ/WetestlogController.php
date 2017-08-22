<?php

namespace App\Http\Controllers\MPZ;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\MPZ\WetestlogRepository;

class WetestlogController extends Controller
{
    //
    private $wetestlog;

    public function __construct(WetestlogRepository $wetestlog)
    {
        $this->wetestlog = $wetestlog;
        $this->program = 'MPZW0010';
        session(['program' => $this->program]);
        $this->middleware('role');
    }

    public function save()
    {
        $this->middleware('role:insert');
        $params = request()->all();
        $result = $this->wetestlog->save($params);
        $response = response()->json($result);
        return $response;
    }

    public function init($point_no)
    {
        $result = $this->wetestlog->init($point_no);
        $response = response()->json($result);
        return $response;
    }
}
