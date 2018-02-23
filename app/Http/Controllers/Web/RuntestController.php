<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Exception;
use App\Traits\Common;
use App\Services\MPZ\OverdueService;
use DB;

class RuntestController extends Controller
{
    use Common;

    private $overdue;

    public function __construct(OverdueService $overdue)
    {
        $this->overdue = $overdue;
    }
    
    public function test()
    {
        $a = $this->overdue->notice2200();
        return $a;
    }
}
