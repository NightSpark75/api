<?php

namespace App\Http\Controllers\MPB\Production;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\MPB\Production\WorkOrderRepository;

class WorkOrderController extends Controller
{
    //
    private $work;
    private $program;

    public function __construct(WorkOrderRepository $work) {
        $this->work = $work;
        $this->program = 'MPBW0010';
        session(['program' => $this->program]);
        $this->middleware('role');
    }

    public function init() {
        $result = $this->work->init();
        $response = response()->json($result);
        return $response;
    }
}
