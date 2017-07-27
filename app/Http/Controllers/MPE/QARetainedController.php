<?php

namespace App\Http\Controllers\MPE;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\MPE\QARetainedRepository;

class QARetainedController extends Controller
{
    //
    private $retained;

    public function __construct(QARetainedRepository $retained) {
        $this->retained = $retained;
        $this->program = 'MPEW0010';
        session(['program' => $this->program]);
        $this->middleware('role');
    }

    public function getList()
    {
        $result = $this->retained->getList();
        return $result;
    }
}