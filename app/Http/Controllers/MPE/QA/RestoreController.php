<?php

namespace App\Http\Controllers\MPE\QA;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\MPE\QA\RestoreRepository;

class RestoreController extends Controller
{
    //
    private $restore;
    private $program;

    public function __construct(RestoreRepository $restore) {
        $this->restore = $restore;
        $this->program = 'MPEW0050';
        session(['program' => $this->program]);
        $this->middleware('role');
    }

    public function getList() 
    {
        $result = $this->restore->getReceiveList();
        return $result;
    }

    public function posting() 
    {
        $input = request()->all();
        $barcode = $input['barcode'];
        $qty = $input['qty'];
        $result = $this->restore->restoreSave($barcode, $qty);
        return $result;
    }
}
