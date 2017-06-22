<?php
/**
 * 檔案上傳與下載相關邏輯
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/06/22
 * @since 1.0.0 spark: 完成檔案上傳與下載功能
 * 
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\FileRepository;

/**
 * Class FileController
 *
 * @package App\Http\Controllers
 */
class FileController extends Controller
{
    /**
     * file upload
     * 
     * @param Request $req request
     * @return array
     */
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

    /**
     * file download
     * 
     * @param string $token load file token
     * @param string $file_id load file id
     * @param string $user_id user id
     * @return response
     */
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
