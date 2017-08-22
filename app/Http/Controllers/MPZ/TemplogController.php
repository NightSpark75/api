<?php

namespace App\Http\Controllers\MPZ;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\MPZ\TemplogRepository;

class TemplogController extends Controller
{
    //
    private $templog;

    public function __construct(TemplogRepository $templog)
    {
        $this->templog = $templog;
        $this->program = 'MPZW0010';
        session(['program' => $this->program]);
        $this->middleware('role');
    }

    public function save()
    {
        $this->middleware('role:insert');
        $params = request()->all();
        $result = $this->templog->save($params);
        $response = response()->json($result);
        return $response;
    }

    public function init($point_no)
    {
        $result = $this->templog->init($point_no);
        $response = response()->json($result);
        return $response;
    }
}
