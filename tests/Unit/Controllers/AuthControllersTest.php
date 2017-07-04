<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Controllers\AuthController;
use App\Repositories\AuthRepository;
use App\Traits\Sqlexecute;
use Auth;

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
    public function testLogin()
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

    /**
     * test logout.
     *
     * @return void
     */
    public function testLogout()
    {
        /** arrange */
        $user_id = str_random(10);

        /** act */
        $this->app->instance(AuthController::class, $this->mock);
        $this->mock->shouldReceive('logout')
            ->once()
            ->withAnyArgs();
        Auth::loginUsingId($user_id, false);
        $this->target->logout();
        $actual = Auth::check();
        /** assert */
        $this->assertFalse($actual);
    }
}
