<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Controllers\AuthController;
use App\Repositories\AuthRepository;
use App\Traits\Sqlexecute;

class AuthControllersTest extends TestCase
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
        $this->mock = $this->initMock(AuthRepository::class);
        $this->target = $this->app->make(AuthController::class);
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

    public function testExample()
    {
        $this->assertTrue(true);
    }
    
    /**
     * test login.
     *
     * @return void
     */
    public function _test_login()
    {
        /** arrange */
        $result = ['result' => true, 'msg' => 'unit test'];
        $expected = response()->json($result);

        /** act */
        $this->app->instance(AuthController::class, $this->mock);
        $this->mock->shouldReceive('login')
            ->once()
            ->withAnyArgs()
            ->andReturn($result);
        $actual = $this->target->login();
        /** assert */
        $this->assertEquals($expected->getData(), $actual->getData());
    }
}
