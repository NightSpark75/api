<?php

namespace App\Http\Controllers\MPP;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PickingController extends Controller
{
    //
    private $picking;

    public function __construct(PickingRepository $picking)
    {
        $this->picking = $picking;
        $this->middleware('jwt:role');
    }

    
}
