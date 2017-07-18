<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Controllers\Web\FileController;
use App\Repositories\Web\FileRepository;
use App\Traits\Sqlexecute;

class FileControllerTest extends TestCase
{
    use DatabaseTransactions;
    use Sqlexecute;

    private $mock;
    private $target;

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
    public function test_upload_file()
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
     * test downloadFile. return attachment
     *
     * @return void
     */
    public function test_download_file_attachment()
    {
        /** arrange */
        $file = new \stdClass();
        $file->code = 'code';
        $file->mime = 'mime';
        $file->name = 'name';
        $file->extension = 'extension';
        $file->path = 'path';
        $file->transform = 'transform';
        $file->store_type = 'C';
        $result = ['result' => true, 'msg' => 'download file', 'file' => $file];
        $expected = response(base64_decode('code'))
            ->header('Content-Type', 'mime') // MIME
            ->header('Content-length', strlen('code')) // base64
            ->header('Content-Disposition', 'attachment; filename=' . 'name') // file_name
            ->header('Content-Transfer-Encoding', 'binary');
        /** act */
        $this->app->instance(FileController::class, $this->mock);
        $this->mock->shouldReceive('downloadFile')
            ->once()
            ->withAnyArgs()
            ->andReturn($result);
        $actual = $this->target->downloadFile('token', 'file_id', 'user_id');

        /** assert */
        $this->assertEquals($expected->getOriginalContent(), $actual->getOriginalContent());
    }

    /**
     * test downloadFile. return attachment
     *
     * @return void
     */
    public function test_download_file_path_attachment()
    {
        /** arrange */
        $file = new \stdClass();
        $file->code = 'code';
        $file->mime = 'text/plain';
        $file->name = 'text.txt';
        $file->extension = 'txt';
        $file->path = dirname(dirname(dirname(__FILE__))).'/temp';
        $file->transform = 'FileRepository@upload.tmp';
        $file->store_type = 'P';
        $result = ['result' => true, 'msg' => 'download file', 'file' => $file];
        $headers = ['Content-Type' => $file->mime];
        $expected = response()->download($file->path.'/'.$file->transform, $file->name, $headers);

        /** act */
        $this->app->instance(FileController::class, $this->mock);
        $this->mock->shouldReceive('downloadFile')
            ->once()
            ->withAnyArgs()
            ->andReturn($result);
        $actual = $this->target->downloadFile('token', 'file_id', 'user_id');

        /** assert */
        $this->assertEquals($expected->getFile(), $actual->getFile());
    }

    /**
     * test downloadFile. online open file
     *
     * @return void
     */
    public function test_download_file_open()
    {
        /** arrange */
        $file = new \stdClass();
        $file->code = 'code';
        $file->mime = 'mime';
        $file->name = 'name';
        $file->extension = 'pdf';
        $file->path = 'path';
        $file->transform = 'transform';
        $file->store_type = 'C';
        $result = ['result' => true, 'msg' => 'download file', 'file' => $file];
        $expected = response(base64_decode('code'))
            ->header('Content-Type', 'mime') // MIME
            ->header('Content-length', strlen('code')) // base64
            ->header('Content-Transfer-Encoding', 'binary');

        /** act */
        $this->app->instance(FileController::class, $this->mock);
        $this->mock->shouldReceive('downloadFile')
            ->once()
            ->withAnyArgs()
            ->andReturn($result);
        $actual = $this->target->downloadFile('token', 'file_id', 'user_id');

        /** assert */
        $this->assertEquals($expected->getOriginalContent(), $actual->getOriginalContent());
    }

    /**
     * test downloadFile. online open file
     *
     * @return void
     */
    public function test_download_file_path_open()
    {
        /** arrange */
        $file = new \stdClass();
        $file->code = 'code';
        $file->mime = 'application/pdf';
        $file->name = 'test.pdf';
        $file->extension = 'pdf';
        $file->path = 'tests/temp';
        $file->transform = 'FileRepository@upload.tmp';
        $file->store_type = 'P';
        $result = ['result' => true, 'msg' => 'download file', 'file' => $file];
        $headers = ['Content-Type' => $file->mime];
        $expected = response()->file($file->path.'/'.$file->transform, $headers);
        /** act */
        $this->app->instance(FileController::class, $this->mock);
        $this->mock->shouldReceive('downloadFile')
            ->once()
            ->withAnyArgs()
            ->andReturn($result);
        $actual = $this->target->downloadFile('token', 'file_id', 'user_id');

        /** assert */
        $this->assertEquals($expected->getFile(), $actual->getFile());
    }

    /**
     * test downloadFile. result false
     *
     * @return void
     */
    public function test_download_file_result_false()
    {
        /** arrange */
        $result = ['result' => false, 'msg' => 'result false'];
        $expected = view('error')->with('message', $result['msg']);

        /** act */
        $this->app->instance(FileController::class, $this->mock);
        $this->mock->shouldReceive('downloadFile')
            ->once()
            ->withAnyArgs()
            ->andReturn($result);
        $actual = $this->target->downloadFile('token', 'file_id', 'user_id');

        /** assert */
        $this->assertEquals($expected, $actual);
    }
}
