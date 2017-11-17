<?php
namespace App\Http\Controllers\MPB\Production;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\MPB\Production\CleanRepository;

class CleanController extends Controller
{
    //   
    private $work;
    private $program;

    public function __construct(CleanRepository $work) {
        $this->work = $work;
        $this->program = 'MPBW0040';
        session(['program' => $this->program]);
        $this->middleware('role');
    }

    public function getCleanJob() 
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

    public function dept($deptno)
    {
        $result = $this->work->getDept($deptno);
        $response = response()->json($result);
        return $response;
    }

    public function member($sno, $deptno)
    {
        $result = $this->work->getMember($sno, $deptno);
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
}
