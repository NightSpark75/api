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

    public function getJob() 
    {
        $result = $this->work->getJob();
        $response = response()->json($result);
        return $response;
    }

    public function compare() 
    {
        $input = request()->all();
        $job_list = json_decode($input['job_list'], true);
        $result = $this->work->compare($job_list);
        $response = response()->json($result);
        return $response;
    }

    public function member($sno, $psno)
    {
        $result = $this->work->getMember($sno, $psno);
        $response = response()->json($result);
        return $response;
    }
}
