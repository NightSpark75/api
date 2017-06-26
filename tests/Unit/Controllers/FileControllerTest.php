<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Controllers\FileController;
use App\Repositories\FileRepository;
use App\Traits\Sqlexecute;

class FileControllerTest extends TestCase
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
        $this->mock = $this->initMock(FileRepository::class);
        $this->target = $this->app->make(FileController::class);
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
     * test uploadFile.
     *
     * @return void
     */
    public function test_uploadFile()
    {
        /** arrange */
        $result = ['result' => true, 'msg' => 'unit test'];
        $expected = response()->json($result);

        /** act */
        $this->app->instance(FileController::class, $this->mock);
        $this->mock->shouldReceive('uploadFile')
            ->once()
            ->withAnyArgs()
            ->andReturn($result);
        $actual = $this->target->uploadFile();
        /** assert */
        $this->assertEquals($expected->getData(), $actual->getData());
    }

    /**
     * test downloadFile.
     *
     * @return void
     */
    public function test_downloadFile()
    {
        /** arrange */
        $pars = [':name' => 'test', ':dis' => 'test dis', ':user' => 'test_user', ':pre' => '', ':id' => '', ':res' => '', ':msg' => ''];
        $pars = $this->procedure('pk_common.get_new_file_id', $pars);
        $file_id = $pars[':id'];

        $bindings = [$file_id];
        $upload_query = "
            update api_file_base 
                set status = 'S', updated_by = created_by, updated_at = CURRENT_TIMESTAMP 
            where id = ?
        ";
        $this->query($bindings, $upload_query);

        $upload_query = "
            update api_file_code
                set 
                    name = 'test.txt', 
                    extension = 'txt',
                    mime = 'text/plain',
                    code = 'MTIz',
                    updated_by = created_by, 
                    updated_at = CURRENT_TIMESTAMP
            where file_id = ?
        ";
        $this->query($bindings, $upload_query);

        $pars = [':id' => $file_id, ':user' => 'test_user', ':token' => '', ':md5_user' => '', ':md5_id' => '',  ':res' => '', ':msg' => ''];
        $pars = $this->procedure('pk_common.get_file_token', $pars);
        $token = $pars[':token'];
        $md5_file_id = $pars[':md5_id'];
        $md5_user_id = $pars[':md5_user'];

        $file_query = "select name, code, mime, extension from api_file_code where file_id = '$file_id'";
        $file = $this->select($file_query);

        $result = ['result' => true, 'msg' => 'download file', 
            'file' => [
                'code' => $file->code,
                'mime' => $file->mime,
                'name' => $file->name,
                'extension' => $file->extension, 
            ]
        ];
        $expected = response(base64_decode($file->code))
            ->header('Content-Type', $file->mime) // MIME
            ->header('Content-length', strlen($file->code)) // base64
            ->header('Content-Disposition', 'attachment; filename=' . $file->name) // file_name
            ->header('Content-Transfer-Encoding', 'binary');

        /** act */
        $this->app->instance(FileController::class, $this->mock);
        $this->mock->shouldReceive('downloadFile')
            ->once()
            ->withAnyArgs()
            ->andReturn($result);
        $actual = $this->target->downloadFile($token, $md5_file_id, $md5_user_id);

        /** assert */
        $this->assertEquals($expected, $actual);
    }
}
