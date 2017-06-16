<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    //

    function __construct()
    {

    }

    function uploadFile(Request $req)
    {
        $x = request()->input();
        $f = request()->file('file_data');
        $a = $req->all();
        $t = request()->hasFile('file_data');
    }

    function downloadFile()
    {
        
    }
}
