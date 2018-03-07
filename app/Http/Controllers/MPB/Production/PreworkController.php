<?php
namespace App\Http\Controllers\MPB\Production;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\MPB\Production\PreworkRepository;

class PreworkController extends Controller
{
    //   
    private $work;
    private $program;

    public function __construct(ProductionRepository $work) {
        $this->work = $work;
        $this->program = 'MPBW0050';
        session(['program' => $this->program]);
        $this->middleware('role');
    }

    public function getProduction() 
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

    public function joinWorking()
    {
        $input = request()->all();
        $result = $this->work->joinWorking($input);
        $response = response()->json($result);
        return $response;
    }

    public function leaveWorking()
    {
        $input = request()->all();
        $result = $this->work->leaveWorking($input);
        $response = response()->json($result);
        return $response;
    }

    public function allJoinWorking()
    {
        $input = request()->all();
        $result = $this->work->allJoinWorking($input);
        $response = response()->json($result);
        return $response;
    }

    public function allLeaveWorking()
    {
        $input = request()->all();
        $result = $this->work->allLeaveWorking($input);
        $response = response()->json($result);
        return $response;
    }

    public function workComplete()
    {
        $input = request()->all();
        $result = $this->work->workComplete($input);
        $response = response()->json($result);
        return $response;
    }

    public function material($sno, $psno)
    {
        $result = $this->work->getMaterial($sno, $psno);
        $response = response()->json($result);
        return $response;
    }

    public function checkMaterial()
    {
        $input = request()->all();
        $sno = $input['sno'];
        $psno = $input['psno'];
        $result = $this->work->checkMaterial($sno, $psno);
        $response = response()->json($result);
        return $response;
    }
}
