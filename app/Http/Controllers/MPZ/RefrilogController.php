<?php

namespace App\Http\Controllers\MPZ;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\MPZ\RefrilogRepository;

class RefrilogController extends Controller
{
    //
    private $refrilog;

    public function __construct(RefrilogRepository $refrilog)
    {
        $this->refrilog = $refrilog;
        $this->program = 'MPZW0010';
        session(['program' => $this->program]);
        $this->middleware('role');
    }

    public function save()
    {
        $this->middleware('role:insert');
        $params = request()->all();
        $result = $this->refrilog->save($params);
        $response = response()->json($result);
        return $response;
    }

    public function init($point_no)
    {
        $result = $this->refrilog->init($point_no);
        $response = response()->json($result);
        return $response;
    }
}
