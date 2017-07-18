<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Controllers\Web\AuthController;
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
    public function test_login()
    {
        /** arrange */
        $result = ['result' => true, 'msg' => 'unit test'];
        $response = response()->json($result);
        $expected = $response->getData();

        /** act */
        $this->app->instance(AuthController::class, $this->mock);
        $this->mock->shouldReceive('login')
            ->once()
            ->withAnyArgs()
            ->andReturn($result);
        $action = $this->target->login();
        $actual = $action->getData();

        /** assert */
        $this->assertEquals($expected, $actual);
    }

    /**
     * test logout.
     *
     * @return void
     */
    public function test_logout()
    {
        /** arrange */
        $expected = redirect()->route('thanks');

        /** act */
        $this->app->instance(AuthController::class, $this->mock);
        $this->mock->shouldReceive('logout')
            ->once()
            ->withAnyArgs();
        $actual = $this->target->logout();

        /** assert */
        $this->assertEquals($expected, $actual);
    }

    /**
     * test get menu when no login.
     *
     * @return void
     */
    public function test_menu_no_login()
    {
        /** arrange */
        $result = ['result' => false, 'msg' => '尚未登入，無法取得功能清單!(#0001)'];
        $response = response()->json($result);
        $expected = $response->getData();

        /** act */
        $action = $this->target->menu();
        $actual = $action->getData();

        /** assert */
        $this->assertEquals($expected, $actual);
    }

    /**
     * test get menu when logged.
     *
     * @return void
     */
    public function test_menu_logged()
    {
        /** arrange */
        $user_id = str_random(10);
        $user_pw = str_random(10);
        $user_name = str_random(10);
        $bindings = [
            'user_id' => $user_id,
            'user_pw' => $user_pw,
            'user_name' => $user_name,
        ];
        $insert_query = "
            insert into sma_user_m (co, user_id, user_pw, user_name, state)
                values ('C01', :user_id, :user_pw, :user_name, 'Y')
        ";
        $this->query($bindings, $insert_query);
        Auth::loginUsingId($user_id, false);
        $menu = [];
        $result = ['result' => true, 'msg' => '已取得清單!(#0000)', 'menu' => $menu];
        $response = response()->json($result);
        $expected = $response->getData();

        /** act */
        $this->app->instance(AuthController::class, $this->mock);
        $this->mock->shouldReceive('getMenu')
            ->once()
            ->withAnyArgs()
            ->andReturn($result);
        $action = $this->target->menu();
        $actual = $action->getData();

        /** assert */
        $this->assertEquals($expected, $actual);
        Auth::logout();
    }
}
