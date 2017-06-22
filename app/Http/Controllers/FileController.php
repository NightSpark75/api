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
        $extention = $file->getClientOriginalExtension();
        $mime = $file->getMimeType();
        $data = file_get_contents($file);
        $code = base64_encode($data);
        $this->file->set_upload_file_data($id, $user, $name, $extention, $mime, $code);
        //$this->file->set_upload_file_data($id, $user, $name, $extention, $mime, $file);
    }

    function downloadFile(Request $req)
    {
        $input = $req->input();
        $token = $input['token'];
        $file_id = $input['file_id'];
        $user_id = $input['user_id'];
        $file_info = $this->file->get_file_info($token, $file_id, $user_id);
        
        $mime = $file_info->mime;
        $name = $file_info->name;
        $code = base64_decode($file_info->code);

        return response($code)
            ->header('Content-Type', $mime) // MIME
            ->header('Content-length', strlen($code)) // base64
            ->header('Content-Disposition', 'attachment; filename=' . $name) // file_name
            ->header('Content-Transfer-Encoding', 'binary');
    }

    function testproc()
    {
        $pdo = DB::getPdo();
        //$pdo = $this->db->getPdo();

        $name = 'test';
        $dis = 'test proc';
        $user = '106013';
        $pre = '';
        $result;
        $msg;
        

        $proc = $pdo->prepare("begin pk_common.get_new_file_id(:name, :dis, :user, :pre, :id, :result, :msg); end;");
        // in
        $proc->bindValue(':name', $name, PDO::PARAM_STR, 30);
        $proc->bindParam(':dis', $dis, PDO::PARAM_STR, 200);
        $proc->bindParam(':user', $uesr, PDO::PARAM_STR, 10);
        $proc->bindParam(':pre', $pre, PDO::PARAM_STR, 32);
        // out
        $proc->bindParam(':id', $id, PDO::PARAM_STR, 32);
        $proc->bindParam(':result', $result, PDO::PARAM_STR, 10);
        $proc->bindParam(':msg', $msg, PDO::PARAM_STR, 1000);

        $proc->execute();
        return ['name' => $name, 
                'dis' => $dis, 
                'user' => $user, 
                'pre' => $pre, 
                'id' => $id, 
                'result' => $result,
                'msg' => $msg];
    }

    function g_test()
    {
        //$pdo = DB::getPdo();
        $p1 = 'test';
        $proc = $this->pdo->prepare('begin pk_common.g_test(:p1, :p2); end;');
        $proc->bindValue(':p1', $p1, PDO::PARAM_STR, 10);
        $proc->bindParam(':p2', $p2, PDO::PARAM_STR, 10);
        $proc->execute();
        return (string)$p2;
    }
}
