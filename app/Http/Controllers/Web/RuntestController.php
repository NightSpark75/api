<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Exception;
use App\Traits\Common;
use App\Services\MPE\QA\OverdueService;

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
        //$a = $this->overdue->overdueNotice();
        return 'hello test';
    }
}
