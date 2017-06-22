<?php
/**
 * File相關資料邏輯處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/06/20
 * @since 1.0.0 spark: 建立檔案寫入與讀取相關procedure
 * 
 */
namespace App\Repositories;

use DB;
use PDO;

/**
 * Class FileRepository
 *
 * @package App\Repositories
 */
class FileRepository
{   
    public function __constructure() 
    {

    }

    /**
     * 執行SQL語法
     * 
     * @param array $bindings parames
     * @param string $query query string
     * @return 
     */
    public function query($bindings, $query) 
    {
        $statement = DB::getPdo()->prepare($query);
        DB::bindValues($statement, DB::prepareBindings($bindings));
        $statement->execute();
        return ['result' => true, $msg = 'execute query success!!'];
    }

    /**
     * 執行SQL查詢
     * 
     * @param string $query query string
     * @return 
     */
    public function select($query)
    {
        return DB::selectOne($query);
    }

    /**
     * 寫入檔案資料
     * 
     * @param string $id file id
     * @param string $user user id
     * @param string $name name 檔名
     * @param string $extension extension副檔名
     * @param string $mime MIME 檔案型態
     * @param string $code base64 code
     * @return 
     */
    public function set_upload_file_data($id, $user, $name, $extension, $mime, $code) 
    {
        $user_query = 
            'select created_by as "user" 
                from api_file_base 
                where id = \''.$id.'\'';
        $created_user = $this->select($user_query)->user;

        $md5_query = 
            'select pk_common.get_md5(\''.$created_user.'\') as "md5" 
                from dual';
        $user_md5 = $this->select($md5_query)->md5;

        if ($user_md5 != $user) {
            return ['result' => false, 'msg' => 'not the creator'];
        }

        $code_bindings = [$id, $name, $extension, $mime, $code, $created_user];
        $insert_code_query = 
            'insert into api_file_code 
                    (file_id, name, extension, mime, code, created_by, created_at) 
                values 
                    (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)';
        $insert_code = $this->query($code_bindings, $insert_code_query);

        $file_bindings = [$id];
        $update_file_query = 
            'update api_file_base 
                set status = \'S\', updated_by = created_by , updated_at = CURRENT_TIMESTAMP 
                where id = ?';
        $update_file = $this->query($file_bindings, $update_file_query);

        return $update_file;
    }

    /**
     * 取得檔案base64編碼
     * 
     * @param string $token file token
     * @param string $file_id file id
     * @param string $user user id
     * @return 
     */
    public function get_file_info($token, $file_id, $user)
    {
        $query = 
            'select t.file_id, t.load_user, t.status, c.name, c.extension, c.mime, c.code, t.created_by
                from api_file_token t, api_file_code c
                where t.file_id = c.file_id and file_token \'' . $token . '\'';
        $select = $this->select($query);

        $s_file_id = $select->file_id;
        $s_load_user = $select->load_user;
        $s_status = $select->status;
        $s_name = $select->name;
        $s_extension = $select->extension;
        $s_mime = $select->mime;
        $s_code = $select->code;
        $s_created_by = $select->code;
        
        return [
            'name' => $s_name,
            'file_name' => $s_extension,
            ''
        ];



        $pdo = DB::getPdo();
        
        $proc = $pdo->prepare('begin pk_common.get_file_code(:token, :file_id, :user, :name, :file_name, :mime, :code, :result, :msg); end;');
        // in
        $proc->bindParam(':token', $token);
        $proc->bindParam(':file_id', $file_id);
        $proc->bindParam(':user', $user);
        // out
        $proc->bindParam(':name', $name);
        $proc->bindParam(':file_name', $file_name);
        $proc->bindParam(':mime', $mime);
        $proc->bindParam(':code', $code);
        $proc->bindParam(':result', $result);
        $proc->bindParam(':msg', $msg);

        $proc->execute();
    }

}