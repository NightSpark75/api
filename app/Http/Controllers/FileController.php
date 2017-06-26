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

use App\Repositories\FileRepository;

/**
 * Class FileController
 *
 * @package App\Http\Controllers
 */
class FileController extends Controller
{
    private $file;
    private $online_open = ['pdf'];

    /**
     * construct
     * 
     * @param FileRepository $file
     * @return void
     */
    public function __construct(FileRepository $file)
    {
        $this->file = $file;
    }

    /**
     * file upload
     * 
     * @param Request $req request
     * @return array
     */
    public function uploadFile()
    {
        $file = request()->file('file_data');
        $id = request()->input('file_id');
        $user = request()->input('user_id');
        $result = $this->file->uploadFile($id, $user, $file);
        return response()->json($result);
    }

    /**
     * file download
     * 
     * @param string $token load file token
     * @param string $file_id load file id
     * @param string $user_id user id
     * @return Mixed
     */
    public function downloadFile($token, $file_id, $user_id)
    {
        $result = $this->file->downloadFile($token, $file_id, $user_id);
        if ($result['result']) {
            return $this->setFile($result['file']);
        }
        return view('error')->with('message', $result['msg']);
    }

    /**
     * 建構檔案下載頁面
     * 
     * @param array $file $file_info
     * @return Response
     */
    private function setFile($file)
    {
        $decode = base64_decode($file['code']);
        $code = $file['code'];
        $name = $file['name'];
        $mime = $file['mime'];
        $extension = $file['extension'];

        if (in_array($extension, $this->online_open)) {
            return $this->onlineOpen($decode, $mime, $code);
        }
        return $this->attachmentFile($decode, $mime, $code, $name);
    }

    /**
     * 建構檔案下載頁面
     * 
     * @param string $decode decoded base64
     * @param string $mime file mime
     * @param string $code file base64 code
     * @param string $name file name
     * @return Response
     */
    private function attachmentFile($decode, $mime, $code, $name)
    {
        $response = 
            response($decode)
            ->header('Content-Type', $mime) // MIME
            ->header('Content-length', strlen($code)) // base64
            ->header('Content-Disposition', 'attachment; filename=' . $name) // file_name
            ->header('Content-Transfer-Encoding', 'binary');
        return $response;
    }

    /**
     * 建構檔案直接線上開啟
     * 
     * @param string $decode decoded base64
     * @param string $mime file mime
     * @param string $code file base64 code
     * @return Response
     */
    private function onlineOpen($decode, $mime, $code)
    {
        $response = 
            response($decode)
            ->header('Content-Type', $mime) // MIME
            ->header('Content-length', strlen($code)) // base64
            ->header('Content-Transfer-Encoding', 'binary');
        return $response;
    }
}
