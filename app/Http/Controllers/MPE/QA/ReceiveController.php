<?php

namespace App\Http\Controllers\MPE\QA;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\MPE\QA\ReceiveRepository;

class ReceiveController extends Controller
{
    //
    private $receive;
    private $program;

    public function __construct(ReceiveRepository $receive) {
        $this->receive = $receive;
        $this->program = 'MPEW0030';
        session(['program' => $this->program]);
        $this->middleware('role');
    }

    public function getList() 
    {
        $result = $this->receive->getList();
        return $result;
    }

    public function posting() 
    {
        $input = request()->all();
        $no = $input['no'];
        $result = $this->receive->posting($no);
        return $result;
    }
}
