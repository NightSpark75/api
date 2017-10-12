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

    public function getDetail($lsa_no)
    {
        $result = $this->receive->getDetail($lsa_no);
        return $result;
    }

    public function posting() 
    {
        $input = request()->all();
        $no = $input['no'];
        $item_e = json_decode($input['item_e'], true);
        $result = $this->receive->posting($no, $item_e);
        return $result;
    }
}
