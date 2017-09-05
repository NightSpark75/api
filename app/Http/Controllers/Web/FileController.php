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
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\Web\FileRepository;
use App\Models\Web\User;
use Auth;

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
     * 建構式
     * 
     * @param FileRepository $file
     * @return void
     */
    public function __construct(FileRepository $file)
    {
        $this->file = $file;
    }

    /**
     * 檔案上傳
     * 
     * @param Request $req request
     * @return array
     */
    public function uploadFile($store_type = 'code')
    {
        $file = request()->file('file_data');
        $id = request()->input('file_id');
        $user = request()->input('user_id');
        $result = $this->file->uploadFile($id, $user, $file, $store_type);
        return response()->json($result);
    }

    /**
     * 檔案上傳舊介面
     * 
     * @param Request $req request
     * @return array
     */
    public function uploadOldFile($store_type = 'code')
    {
        $file = request()->file('file');
        $id = request()->input('file_id');
        $user = request()->input('user_id');
        $result = $this->file->uploadFile($id, $user, $file, $store_type);
        if ($result['result']) {
            return view('service.complete')
                ->with('title', '檔案上傳成功!')
                ->with('message', '請關閉此視窗');
        }
        return view('service.complete')
            ->with('title', '檔案上傳失敗，請洽資訊課#6078')
            ->with('message', $result['msg']);
    }

    /**
     * 檔案下載
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
            return $this->getFile($result['file']);
        }
        return view('error')->with('message', $result['msg']);
    }

    /**
     * 取得檔案
     * 
     * @param array $file file info
     * @return Mixed
     */
    private function getFile($file)
    {
        if ($file->store_type == 'P') {
            return $this->loadFile($file);
        }
        return $this->setFile($file);
    }

    /**
     * 讀取實體檔案並下載，或直接開啟檔案
     * 
     * @param array $file file info
     * @return Response
     */
    private function loadFile($file)
    {
        $path = $file->path.'/'.$file->transform;
        $header = ['Content-Type' => $file->mime];
        // 瀏覽器開啟
        if (in_array($file->extension, $this->online_open)) {
            return response()->file($path, $header);
        }
        // 下載檔案
        return response()->download($path, $file->name, $header);
    }

    /**
     * 建構檔案下載頁面，或直接開啟檔案
     * 
     * @param array $file $file_info
     * @return Response
     */
    private function setFile($file)
    {
        $decode = base64_decode($file->code);
        $code = $file->code;
        $name = mb_convert_encoding($file->name,"BIG-5","UTF-8");
        $mime = $file->mime;
        $extension = $file->extension;

        // 建構標頭
        $response = 
            response($decode)
            ->header('Content-Type', $mime) // MIME
            ->header('Content-length', strlen($code)) // base64
            ->header('Content-Transfer-Encoding', 'binary');

        if (in_array($extension, $this->online_open)) {
            return $response;
        }

        // 加入下載檔案標頭
        $response->header('Content-Disposition', 'attachment; filename=' . $name); // file_name
        return $response;
    }
}
