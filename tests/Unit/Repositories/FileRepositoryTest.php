<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Repositories\FileRepository;
use Illuminate\Http\UploadedFile;
use App\Traits\Sqlexecute;

class FileRepositoryTest extends TestCase
{
    use DatabaseTransactions;
    use Sqlexecute;

    private $target;
    /**
     * setUp()
     */
    public function setUp()
    {
        parent::setUp();
        $this->target = $this->app->make(FileRepository::class);
    }
    /**
     * tearDown()
     */
    public function tearDown()
    {
        $this->target = null;
        $this->mock = null;
        parent::tearDown();
    }

    /**
        $songfile = new UploadedFile($temp_filepath, 'foofile.mp3', 'audio/mpeg', 100023, null, $test=true);
        $files = ['songfile' => $songfile,];
        // define $uri, $method, $parameters, $cookies, $server, $content
        $request = Request::create($uri, $method, $parameters, $cookies, $files, $server, $content);
     */

    /**
     * test uploadFile.
     *
     * @return void
     */
    public function test_uploadFile()
    {
        /** arrange */
        $pars = [':name' => 'test', ':dis' => 'test dis', ':user' => 'test_user', ':pre' => '', ':id' => '', ':r_user' => '', ':res' => '', ':msg' => ''];
        $pars = $this->procedure('pk_common.get_new_file_id', $pars);
        $query = "select pk_common.get_md5('test_user') as \"md5\" from dual";
        $md5_user = $this->select($query)->md5;
        $file_path = base_path().'\tests\temp\FileRepository@upload.tmp';
        $file = new UploadedFile($file_path, 'FileRepository@upload.tmp', 'application/pdf', 100023, null, $test=true);
        $store_type = 'code';
        $expected = ['result' => true, 'msg' => '#0000;檔案上傳成功!'];

        /** act */
        $actual = $this->target->uploadFile($pars[':id'], $md5_user, $file, $store_type);

        /** assert */
        $this->assertEquals($expected, $actual);
    }

    public function test_uploadFile_for_path()
    {
        /** arrange */
        $pars = [':name' => 'test', ':dis' => 'test dis', ':user' => 'test_user', ':pre' => '', ':id' => '', ':r_user' => '', ':res' => '', ':msg' => ''];
        $pars = $this->procedure('pk_common.get_new_file_id', $pars);
        $query = "select pk_common.get_md5('test_user') as \"md5\" from dual";
        $md5_user = $this->select($query)->md5;
        $tmp = strtoupper(md5(uniqid(mt_rand(), true))).'.tmp';
        copy(base_path().'\\tests\\temp\\FileRepository@upload.tmp', base_path().'\\tests\\temp\\'.$tmp);
        $file_path = base_path().'\\tests\\temp\\'.$tmp;
        $file = new UploadedFile($file_path, $tmp, 'application/tmp', 100023, null, $test=true);
        $store_type = 'path';
        $expected = ['result' => true, 'msg' => '#0000;檔案上傳成功!'];

        /** act */
        $actual = $this->target->uploadFile($pars[':id'], $md5_user, $file, $store_type);

        /** assert */
        $this->assertEquals($expected, $actual);
    }
    

    /**
     * test uploadFile. error #0001
     *
     * @return void
     */
    public function test_uploadFile_e_0001()
    {
        /** arrange */
        $pars = [':name' => 'test', ':dis' => 'test dis', ':user' => 'test_user', ':pre' => '', ':id' => '', ':r_user' => '', ':res' => '', ':msg' => ''];
        $pars = $this->procedure('pk_common.get_new_file_id', $pars);
        $bindings = [$pars[':id']];
        $upload_query = "
            update api_file_base 
                set status = 'S', updated_by = created_by, updated_at = CURRENT_TIMESTAMP 
            where id = ?
        ";
        $this->query($bindings, $upload_query);
        $query = "select pk_common.get_md5('test_user') as \"md5\" from dual";
        $md5_user = $this->select($query)->md5;
        $file_path = base_path().'\tests\temp\FileRepository@upload.tmp';
        $file = new UploadedFile($file_path, 'FileRepository@upload.tmp', 'application/pdf', 100023, null, $test=true);
        $store_type = 'code';
        $expected = ['result' => false, 'msg' => '#0001;檔案已上傳成功，無法重複上傳'];

        /** act */
        $actual = $this->target->uploadFile($pars[':id'], $md5_user, $file, $store_type);

        /** assert */
        $this->assertEquals($expected, $actual);
    }

    /**
     * test uploadFile. error #0002
     *
     * @return void
     */
    public function test_uploadFile_e_0002()
    {
        /** arrange */
        $pars = [':name' => 'test', ':dis' => 'test dis', ':user' => 'test_user', ':pre' => '', ':id' => '', ':r_user' => '', ':res' => '', ':msg' => ''];
        $pars = $this->procedure('pk_common.get_new_file_id', $pars);
        $query = "select pk_common.get_md5('error_user') as \"md5\" from dual";
        $md5_user = $this->select($query)->md5;
        $file_path = base_path().'\tests\temp\FileRepository@upload.tmp';
        $file = new UploadedFile($file_path, 'FileRepository@upload.tmp', 'application/pdf', 100023, null, $test=true);
        $store_type = 'code';
        $expected = ['result' => false, 'msg' => '#0002;檔案驗證資訊有誤，您無權限上傳該檔案!'];

        /** act */
        $actual = $this->target->uploadFile($pars[':id'], $md5_user, $file, $store_type);

        /** assert */
        $this->assertEquals($expected, $actual);
    }

    /**
     * test uploadFile. error #0003
     *
     * @return void
     */
    public function test_uploadFile_e_0003()
    {
        /** arrange */
        $pars = [':name' => 'test', ':dis' => 'test dis', ':user' => 'test_user', ':pre' => '', ':id' => '', ':r_user' => '', ':res' => '', ':msg' => ''];
        $pars = $this->procedure('pk_common.get_new_file_id', $pars);
        $query = "select pk_common.get_md5('error_user') as \"md5\" from dual";
        $md5_user = $this->select($query)->md5;
        $file_path = base_path().'\tests\temp\FileRepository@upload.tmp';
        $file = new UploadedFile($file_path, 'FileRepository@upload.tmp', 'application/pdf', 100023, null, $test=true);
        $store_type = 'code';
        $expected = ['result' => false, 'msg' => '#0003;查詢不到檔案資料!'];

        /** act */
        $actual = $this->target->uploadFile('error_file_id', $md5_user, $file, $store_type);

        /** assert */
        $this->assertEquals($expected, $actual);
    }

    /**
     * test downloadFile.
     *
     * @return void
     */
    public function test_downloadFile()
    {
        /** arrange */
        $file_id = $this->initDownloadData();
        $pars =  $this->initToken($file_id);
        
        $token = $pars[':token'];
        $md5_file_id = $pars[':md5_id'];
        $md5_user_id = $pars[':md5_user'];

        $file = $this->getFileContent($token, $md5_file_id, $md5_user_id);
        $result = ['result' => true, 'msg' => '#0005;檔案資料截取成功!', 'file' => $file];
        $expected = $result;

        /** act */
        $actual = $this->target->downloadFile($token, $md5_file_id, $md5_user_id);

        /** assert */
        $this->assertEquals($expected, $actual);
    }

    /**
     * test downloadFile. error #0006
     *
     * @return void
     */
    public function test_downloadFile_e_0006()
    {
        /** arrange */
        $file_id = $this->initDownloadData();
        $pars =  $this->initToken($file_id);
        
        $token = $pars[':token'];
        $md5_file_id = $pars[':md5_id'];
        $md5_user_id = $pars[':md5_user'];

        $result = ['result' => false, 'msg' => '#0006讀取檔案的驗證參數有異常，您無權限讀取此檔!'];
        $expected = $result;

        /** act */
        $actual = $this->target->downloadFile($token, 'error!!!!!', $md5_user_id);

        /** assert */
        $this->assertEquals($expected, $actual);
    }

    /**
     * initDownloadData
     *
     * @return string
     */
    private function initDownloadData()
    {
        // created new file data and return file_id
        $pars = [':name' => 'test', ':dis' => 'test dis', ':user' => 'test_user', ':pre' => '', ':id' => '', ':r_user' => '', ':res' => '', ':msg' => ''];
        $pars = $this->procedure('pk_common.get_new_file_id', $pars);
        $file_id = $pars[':id'];

        // update file base status
        $bindings = [$file_id];
        $upload_query = "
            update api_file_base 
                set status = 'S', updated_by = created_by, updated_at = CURRENT_TIMESTAMP 
            where id = ?
        ";
        $this->query($bindings, $upload_query);

        // update file content data
        $upload_query = "
            update api_file_code
                set name = 'test.txt', extension = 'txt', mime = 'text/plain',
                    code = 'MTIz', updated_by = created_by, updated_at = CURRENT_TIMESTAMP
            where file_id = ?
        ";
        $this->query($bindings, $upload_query);

        return $file_id;
    }

    /**
     * initToken
     *
     * @param string $file_id
     * @return array
     */
    private function initToken($file_id)
    {
        $pars = [':id' => $file_id, ':user' => 'test_user', ':token' => '', ':md5_user' => '', ':md5_id' => '',  ':res' => '', ':msg' => ''];
        $pars = $this->procedure('pk_common.get_file_token', $pars);
        return $pars;
    }

    /**
     * initToken
     *
     * @param string $token
     * @param string $file_id
     * @param string $user
     * @return stdClass
     */
    private function getFileContent($token, $file_id, $user)
    {
        $file_query = "
            select t.file_id, t.load_user, t.status, c.name, c.extension, c.mime, c.code, t.created_by
                from api_file_token t, api_file_code c
                where t.file_id = pk_common.get_md5(c.file_id) and t.status = 'G'
                    and t.file_token = '$token' and t.file_id = '$file_id' and t.load_user = '$user'
        ";
        $file = $this->select($file_query);
        return $file;
    }
}
