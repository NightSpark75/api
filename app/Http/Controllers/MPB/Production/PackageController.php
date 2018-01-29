<?php
namespace App\Http\Controllers\MPB\Production;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\MPB\Production\PackageRepository;

class PackageController extends Controller
{
    //   
    private $work;
    private $program;

    public function __construct(PackageRepository $work) {
        $this->work = $work;
        $this->program = 'MPBW0030';
        session(['program' => $this->program]);
        $this->middleware('role');
    }

    public function getPackage() 
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

    public function member($sno, $psno, $pgno, $duty)
    {
        $result = $this->work->getMember($sno, $psno, $pgno, $duty);
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

    public function dutyClose()
    {
        $input = request()->all();
        $result = $this->work->dutyClose($input);
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

    public function duty($sno, $psno)
    {
        $result = $this->work->getDuty($sno, $psno);
        $response = response()->json($result);
        return $response;
    }
}
