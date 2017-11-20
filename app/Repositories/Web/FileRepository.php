<?php
/**
 * 檔案上傳與下載資料處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/06/22
 * @since 1.0.0 spark: 建立檔案寫入與讀取相關的資料處理
 * 
 */
namespace App\Repositories\Web;

use DB;
use Exception;
use App\Traits\Sqlexecute;

/**
 * Class FileRepository
 *
 * @package App\Repositories
 */
class FileRepository
{   
    use Sqlexecute;

    //private $FILE_PATH = storage_path().'/app/public';
    private $path = '';
    private $transform = '';
    private $store_type = '';
    private $size_limit = 20 * 1024 * 1024; //20MB

    /**
     * 檔案上傳
     * 
     * @param Request $req resquest()
     * @param bool $version version control
     * @return mixed
     */
    public function new_uploadFIle($req, $version)
    {
        try {
            $this->checkFileSize($req);
            $data = $this->setFileData($req, $version);
            if ($data['store_type'] === 'path') {
                $this->store_type = 'P';
                return $this->uploadByPath($data, $version);
            }
            $this->store_type = 'C';
            return $this->uploadByCode($data, $version);
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    /**
     * 檢查檔案大小
     * 
     * @param Request $req resquest()
     * @return mixed
     */
    private function checkFileSize($req)
    {
        $size = $req->file('file')->getClientSize();
        if ($size > $this->size_limit) {
            throw new Exception('檔案大小不得超超過20MB, 您的檔案大小為：' . (string)round($size / (1024 * 1024), 2) . 'MB');
        }
    }

    /**
     * 初始化檔案上傳資料
     * 
     * @param Request $req resquest()
     * @return array
     */
    private function setFileData($req)
    {
        $file = $req->file('file');
        $res['id'] = $req->input('file_id');
        $res['user'] = $req->input('user_id');
        $res['store_type'] = $req->input('store_type');
        $res['name'] = $file->getClientOriginalName();
        $res['extension'] = $file->getClientOriginalExtension();
        $res['mime'] = $file->getMimeType();
        $res['code'] = base64_encode(file_get_contents($file));
        $res['file_size'] = $file->getClientSize();
        return $res;
    }

    /**
     * 上傳檔案以路徑方式儲存
     * 
     * @param array $data data
     * @param bool $version version control
     * @return mixed
     */
    private function uploadByPath($data, $version)
    {
        $this->path = storage_path().'/app/public';
        $this->transform = strtoupper(md5(uniqid(mt_rand(), true)));
        $tmp_name = $transform.'.tmp';
        $file->move($this->path, $tmp_name);
        if ($this->checkDataExists($data['id'])) {
            return updateFile($data, $version);
        }
        return insertFile($data);
    }

    /**
     * 上傳檔案以編碼方式儲存
     * 
     * @param array $data data
     * @param bool $version version control
     * @return mixed
     */
    private function uploadByCode($data, $version)
    {
        if ($this->checkDataExists($data['id'])) {
            return $this->updateFileData($data, $version);
        }
        return $this->insertFileData($data);
    }

    /**
     * 檢查檔案資料是否存在
     * 
     * @param string $id file id
     * @return bool
     */
    private function checkDataExists($id)
    {
        $check = DB::selectOne("
            select count(*) as count
            from api_file_base
            where id = '$id'
        ");
        if ((int)$check->count > 0) {
            return true;
        }
        return false;
    }

    /**
     * 更新檔案資料
     * 
     * @param array $data data
     * @param bool $version version control
     * @return mixed
     */
    private function updateFileData($data, $version)
    {
        try {
            DB::transaction( function () use($data, $version) {
                $command = $this->setSqlCommand($data, false);
                if ($version) {
                    $this->insertVersion($data);
                }
                $this->query($command['bind_base'], $command['sql_base']);
                $this->query($command['bind_code'], $command['sql_code']);
            });
            DB::commit();
            return ['result' => true, 'msg' => '檔案上傳成功!(#0000)'];
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 寫入版本資料
     * 
     * @param array $data data
     * @return mixed
     */
    private function InsertVersion($data)
    {
        try {
            $file_id = $data['id']; 
            $version = DB::selectOne("
                select nvl(max(version) + 1, 1) version
                from api_file_version
                where file_id = '$file_id'
            ")->version;
            DB::insert("
                insert into api_file_version 
                select file_id, $version, extension, mime, code, path, transform, store_type, created_by, updated_by, created_at, updated_at, file_size 
                from api_file_code
                where file_id = '$file_id'
            ");
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 新增檔案資訊
     * 
     * @param array $data data
     * @return mixed
     */
    private function insertFileData($data)
    {
        try {
            $command = $this->setSqlCommand($data, true);
            DB::transaction( function () use($command) {
                $this->query($command['bind_base'], $command['sql_base']);
                $this->query($command['bind_code'], $command['sql_code']);
            });
            DB::commit();
            return ['result' => true, 'msg' => '檔案上傳成功!(#0000)'];
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 回傳SQL語法與資料參數
     * 
     * @param array $data data
     * @param bool $ins insert or update
     * @return array
     */
    private function setSqlCommand($data, $ins)
    {
        $bind_base = [
            'v_name' => $data['name'],
            'v_user' => $data['user'],
            'v_id' => $data['id'],
        ];
        $bind_code = [
            'v_name' => $data['name'],
            'extension' => $data['extension'],
            'mime' => $data['mime'],
            'code' => $data['code'],
            'path' => $this->path,
            'transform' => $this->transform,
            'file_size' => $data['file_size'],
            'store_type' => $this->store_type,
            'v_user' => $data['user'],
            'v_id' => $data['id'],
        ];
        $sql = $this->getSql($ins);
        $sql_base = $sql['base'];
        $sql_code = $sql['code'];
        $command = [
            'sql_base' => $sql_base,
            'sql_code' => $sql_code,
            'bind_base' => $bind_base,
            'bind_code' => $bind_code,
        ];
        return $command;
    }

    /**
     * 回傳SQL語句
     * 
     * @param bool $ins insert or update
     * @return array
     */
    private function getSql($ins)
    {
        if ($ins) {
            return [
                'base' => 
                    "insert into api_file_base (name, status, created_at, created_by, id)
                    values (:v_name, 'S', CURRENT_TIMESTAMP, :v_user, :v_id)",
                'code' => 
                    "insert into api_file_code (name, extension, mime, code, path, transform, file_size, store_type, created_at, created_by, file_id)
                    values (:v_name, :extension, :mime, :code, :path, :transform, :file_size, :store_type, CURRENT_TIMESTAMP, :v_user, :v_id)",
            ];
        }
        return [
            'base' => 
                "update api_file_base
                set name = :v_name, updated_by = :v_user, updated_at = CURRENT_TIMESTAMP
                where id = :v_id",
            'code' => 
                "update api_file_code 
                set name = :v_name, extension = :extension, mime = :mime, code = :code, path = :path, transform = :transform, file_size = :file_size, 
                    store_type = :store_type, updated_by = :v_user, updated_at = CURRENT_TIMESTAMP
                where file_id = :v_id",
        ];
    }

    /**
     * 檔案上傳
     * 
     * @param string $id file id
     * @param string $user user id
     * @param uploadFile $file  檔案物件
     * @param boolean $store_type 以路徑方式儲存
     * @return array
     */
    public function uploadFile($id, $user, $file, $store_type)
    {
        try {
            $created_user = $this->checkUpload($id, $user);
            $this->storeType($id, $file, $created_user, $store_type);
            $this->changeFileStatus($id);
            return ['result' => true, 'msg' => '檔案上傳成功!(#0000)'];
        } catch (Exception $e) {
            return ['result' => false, 'msg' => $e->getMessage()];
        }
    }

    /**
     * 上傳前資料檢核
     * 
     * @param string $id file id
     * @param string $user user id
     * @return string created user id
     */
    private function checkUpload($id, $user)
    {
        $file_base = $this->getFileUser($id);
        $created_user = $file_base->user;
        $status = $file_base->status;
        $user_md5 = $this->userToMD5($created_user);

        if($status == 'S') {
            throw new Exception('檔案已上傳成功，無法重複上傳!(#0001)');
        }

        if ($user_md5 != $user) {
            throw new Exception('檔案驗證資訊有誤，您無權限上傳該檔案!(#0002)');
        }
        return $created_user;
    }

    /**
     * 取得建檔使用者與狀態
     * 
     * @param string $id file id
     * @return array user and status
     */
    private function getFileUser($id)
    {
        $query = "
            select created_by as \"user\", status 
                from api_file_base
                where id = '$id'
            ";
        $result = $this->select($query);
        if (isset($result)) {
            return $result;
        }
        throw new Exception('查詢不到檔案資料!(#0003)');
    }

    /**
     * 使用者id md5加密
     * 
     * @param string $id file id
     * @return string
     */
    private function userToMD5($user)
    {
        $query = "select pk_common.get_md5('$user') as \"md5\" from dual";
        $result = $this->select($query);
        return $result->md5;
    }

    /**
     * 依儲存方式進行存檔
     * 
     * @param string $id file id
     * @param uploadFile $file 檔案內容
     * @param string $created_user user id
     * @param string $store_type 儲存方式
     * @return string
     */
    private function storeType($id, $file, $created_user, $store_type)
    {
        if ($store_type == 'path') {
            $this->copyFile($id, $file, $created_user);
            return;
        }
        $this->insertFile($id, $file, $created_user);
    }

    /**
     * 複製檔案到伺服器
     * 
     * @param string $id file id
     * @param uploadFile $file 檔案內容
     * @param string $created_user user id
     * @return array
     */
    private function copyFile($id, $file, $created_user)
    {
        $transform = strtoupper(md5(uniqid(mt_rand(), true)));
        $file_path = storage_path().'/app/public';
        $tmp_name = $transform.'.tmp';

        $bindings['name'] = $file->getClientOriginalName();
        $bindings['extension'] = $file->getClientOriginalExtension();
        $bindings['mime'] = $file->getMimeType();
        $bindings['path'] = $file_path;
        $bindings['transform'] = $tmp_name;
        $bindings['updated_by'] = $created_user;
        $bindings['file_id'] = $id;

        $file->move($file_path, $tmp_name);

        $query = 
            "update api_file_code 
                set name = :name, extension = :extension, mime = :mime, transform = :transform, path = :path,
                    store_type = 'P', updated_by = :updated_by, updated_at = CURRENT_TIMESTAMP
                where file_id = :file_id
        ";
        $this->query($bindings, $query);       
    }

    /**
     * 寫入檔案資料
     * 
     * @param string $id file id
     * @param uploadFile $file 檔案內容
     * @param string $created_user user id
     * @return array
     */
    private function insertFile($id, $file, $created_user)
    {
        $bindings['name'] = $file->getClientOriginalName();
        $bindings['extension'] = $file->getClientOriginalExtension();
        $bindings['mime'] = $file->getMimeType();
        $bindings['code'] = base64_encode(file_get_contents($file));
        $bindings['updated_by'] = $created_user;
        $bindings['file_id'] = $id;
        
        $query = 
            "update api_file_code 
                set name = :name, extension = :extension, mime = :mime, code = :code, 
                    store_type = 'C', updated_by = :updated_by, updated_at = CURRENT_TIMESTAMP
                where file_id = :file_id
        ";
        $this->query($bindings, $query);       
    }

    /**
     * 更新檔案資料狀態
     * 
     * @param string $id file id
     * @return array
     */
    private function changeFileStatus($id)
    {
        $bindings = [$id];
        $query = "
            update api_file_base 
                set status = 'S', updated_by = created_by , updated_at = CURRENT_TIMESTAMP 
                where id = ?
        ";
        $this->query($bindings, $query);
    }

    /**
     * 下載檔案
     * 
     * @param string $token file token
     * @param string $file_id file id
     * @param string $user user id
     * @return mix
     */
    public function downloadFile($token, $file_id, $user)
    {
        try {
            $file_info = $this->getFileInfo($token, $file_id, $user);
            $this->updateFileStatus($token);
            return ['result' => true, 'msg' => '檔案資料截取成功!(#0005)', 'file' => $file_info];
        } catch (Exception $e) {
            return ['result' => false, 'msg' => $e->getMessage()];
        }
    }

    /**
     * 取得檔案資料
     * 
     * @param string $token file token
     * @param string $file_id file id
     * @param string $user user id
     * @return array
     */
    private function getFileInfo($token, $file_id, $user)
    {
        $query = "
            select t.file_id, t.load_user, t.status, c.name, c.extension, c.mime, c.code, 
                    c.path, c.transform, c.store_type, t.created_by
                from api_file_token t, api_file_code c
                where t.file_id = pk_common.get_md5(c.file_id) and t.status = 'G'
                    and t.file_token = '$token' and t.file_id = '$file_id' and t.load_user = '$user'
        ";
        $file_info = $this->select($query);

        if ($file_info == null) {
            throw new Exception('讀取檔案的驗證參數有異常，您無權限讀取此檔!(#0006)');
        }
        return $file_info;
    }

    /**
     * 更新檔案資訊
     * 
     * @param string $token file token
     * @return void
     */
    private function updateFileStatus($token)
    {
        $binds = [$token];
        $update = "
            update api_file_token
                set status = 'L', updated_by = created_by, updated_at = CURRENT_TIMESTAMP
                where file_token = ?
        ";
        $this->query($binds, $update);
    }

    public function ezGetFile($file_id)
    {
        $file = $this->select("
            select *
            from api_file_code c
            where file_id = '$file_id'
        ");
        return $file;
    }
}