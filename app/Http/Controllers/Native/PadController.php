<?php

namespace App\Http\Controllers\Native;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\Native\PadRepository;

class PadController extends Controller
{
    //
    private $pad;

    public function __construct(PadRepository $pad)
    {
        $this->pad = $pad;
    }

    public function download()
    {
        $result = $this->pad->downloadBundle();
        
        if ($result['result']) {
            $file = $result['file'];
            $decode = base64_decode($file);
            $name = 'index.android.bundle';
    
            $response = response($decode)->header('Content-Disposition', 'attachment; filename=' . $name);
            return $response;
        }
        
        return response()->json([
            'result' => $result['result'],
            'msg' => $result['msg'],
        ]);
    }

    public function version()
    {
        $result = $this->pad->getVersion();
        return response()->json($result);
    }

    public function upload()
    {
        return view('service.bundleUpload');
    }

    public function save()
    {
        $version = request()->input('version');
        $file = request()->file('bundle');
        $result = $this->pad->saveBundle($version, $file);
        return response()->json($result);
    }

}
