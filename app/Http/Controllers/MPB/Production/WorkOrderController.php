<?php

namespace App\Http\Controllers\MPB\Production;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\MPB\Porduction\WorkOrderRepository;

class WorkOrderController extends Controller
{
    //
    private $work;

    public function __construct(WorkOrderRepository $work) {
        $this->work = $work;
    }

    
}
