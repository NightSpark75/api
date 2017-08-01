<?php

namespace App\Http\Controllers\MPE\QC;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\MPE\QC\ReceiveRepository;

class ReceiveController extends Controller
{
    //
    private $receive;
    private $program;

    public function __construct(ReceiveRepository $receive) {
        $this->receive = $receive;
        $this->program = 'MPEW0040';
        session(['program' => $this->program]);
        $this->middleware('role');
    }

    public function init() 
    {
        $result = $this->receive->init();
        return $result;
    }

    public function posting() 
    {
        $input = request()->all();
        $receive_list = json_decode($input['receive_list']);
        $result = $this->receive->posting($receive_list);
        return $result;
    }
}
