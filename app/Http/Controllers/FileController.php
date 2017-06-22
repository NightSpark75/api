<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use PDO;
use App\Repositories\FileRepository;

class FileController extends Controller
{
    //
    private $db;
    private $file;
    private $pdo;

    function __construct(DB $db, FileRepository $file)
    {
        $this->db = $db;
        $this->file = $file;
        $this->pdo = DB::getPdo();
    }

    function uploadFile(Request $req)
    {
        $input = $req->input();
        $file = $req->file('file_data');
        $id = $input['file_id'];
        $user = $input['user_id'];
        $name = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mime = $file->getMimeType();
        $data = file_get_contents($file);
        $code = base64_encode($data);
        $result = $this->file->set_upload_file_data($id, $user, $name, $extension, $mime, $code);
        return $result;
    }

    function downloadFile($token, $file_id, $user_id)
    {
        $result = $this->file->get_file_info($token, $file_id, $user_id);
        
        if ($result['result'] == false) {
            return $result['msg'];
        }

        $mime = $result['info']['mime'];
        $name = $result['info']['name'];
        $code = base64_decode($result['info']['code']);
        $extension = $result['info']['extension'];
        
        $online_open = ['pdf'];

        if (in_array($extension, $online_open)) {
            $response = response($code)
                ->header('Content-Type', $mime) // MIME
                ->header('Content-length', strlen($code)) // base64
                ->header('Content-Transfer-Encoding', 'binary');
        } else {
            $response = response($code)
                ->header('Content-Type', $mime) // MIME
                ->header('Content-length', strlen($code)) // base64
                ->header('Content-Disposition', 'attachment; filename=' . $name) // file_name
                ->header('Content-Transfer-Encoding', 'binary');
        }
        
        return $response;
    }
}
