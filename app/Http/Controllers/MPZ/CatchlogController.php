<?php

namespace App\Http\Controllers\MPZ;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\MPZ\CatchlogRepository;

class CatchlogController extends Controller
{
    //
    private $catchlog;

    public function __construct(CatchlogRepository $catchlog)
    {
        $this->catchlog = $catchlog;
    }

    public function init()
    {
        /*
        if (auth()->check() == false) {
            $data = [
                'result' => false
            ];
            $response = response()->json($data);
            return $response;
        }
        */
        $point = $this->catchlog->getPoint();
        $data = [
            'result' => true,
            'point' => $point,
        ];
        $response = response()->json($data);
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
        $params = request()->all();
        $result = $this->catchlog->insert($params);
        $response = response()->json($result);
        return $response;
    }
}
