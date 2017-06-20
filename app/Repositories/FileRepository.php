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

    public function set_upload_file_data($id, $user, $name, $file_name, $mime, $code) 
    {
        $pdo = DB::getPdo();

        $proc = $pdo->prepare('begin pk_common.set_upload_file_data(:id, :user, :name, :file_name, :mime, :code, :result, :msg)');
        // in
        $proc->bindParam(':id', $id);
        $proc->bindParam(':user', $user);
        $proc->bindParam(':name', $name);
        $proc->bindParam(':file_name', $file_name);
        $proc->bindParam(':mime', $mime);
        $proc->bindParam(':code', $code);
        // out
        $proc->bindParam(':result', $result);
        $proc->bindParam(':msg', $msg);

        $proc->execute();
    }

    public function get_file_code($token, $file_id, $user)
    {
        $pdo = DB::getPdo();
        
        $proc = $pdo->prepare('begin pk_common.get_file_code(:token, :file_id, :user, :name, :file_name, :mime, :code, :result, :msg)');
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