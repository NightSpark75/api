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

    protected $mock;
    protected $target;
    /**
     * setUp()
     */
    public function setUp()
    {
        parent::setUp();
        //$this->mock = $this->initMock(FileRepository::class);
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
        $pars = [':name' => 'test', ':dis' => 'test dis', ':user' => 'test_user', ':pre' => '', ':id' => '', ':res' => '', ':msg' => ''];
        $pars = $this->procedure('pk_common.get_new_file_id', $pars);
        $query = "select pk_common.get_md5('test_user') as \"md5\" from dual";
        $md5_user = $this->select($query)->md5;
        $path = base_path().'\tests\temp\FileRepository@upload.tmp';
        $file = new UploadedFile($path, 'FileRepository@upload.tmp', 'application/pdf', 100023, null, $test=true);
        $expected = ['result' => true, 'msg' => '#0000;檔案上傳成功!'];

        /** act */
        $actual = $this->target->uploadFile($pars[':id'], $md5_user, $file);
        //--dd($actual['data']['result']);
        /** assert */
        $this->assertEquals($expected, $actual);
    }


}
