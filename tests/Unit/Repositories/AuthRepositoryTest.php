<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Repositories\AuthREpository;
use App\Traits\Sqlexecute;
use Auth;
use App\Models\UserPrg;


class AuthRepositoryTest extends TestCase
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
        $this->target = $this->app->make(AuthRepository::class);
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
     * test login success
     */
    public function testLogin()
    {
        /** arrange */
        $expected = ['result' => true, 'msg' => '登入成功!(#0000)'];
        
        $user_id = str_random(10);
        $user_pw = str_random(10);
        $user_name = str_random(10);
        $sys = 'ppm';

        $bindings = [
            'user_id' => $user_id,
            'user_pw' => $user_pw,
            'user_name' => $user_name,
        ];
        $insert_query = "
            insert into sma_user_m (co, user_id, user_pw, user_name, state)
                values ('C99', :user_id, :user_pw, :user_name, 'Y')
        ";
        $this->query($bindings, $insert_query);
        
        /** act */
        $actual = $this->target->login($user_id, $user_pw, $sys);
        $actual_user = Auth::check();

        /** assert */
        $this->assertEquals($expected, $actual);
        $this->assertTrue($actual_user);
        Auth::logout();
    }

    /**
     * test login fail
     */
    public function testLoginFail()
    {
        /** arrange */
        $expected = ['result' => false, 'msg' => '帳號或密碼錯誤!(#0001)'];
        
        $user_id = str_random(10);
        $user_pw = str_random(10);
        $user_name = str_random(10);
        
        /** act */
        $actual = $this->target->login($user_id, $user_pw, $user_name);

        /** assert */
        $this->assertEquals($expected, $actual);
    }

    /**
     * test logout
     */
    public function testLogout()
    {
        /** arrange */
        $expected = false;
        
        $user_id = str_random(10);
        $user_pw = str_random(10);
        $user_name = str_random(10);

        $bindings = [
            'user_id' => $user_id,
            'user_pw' => $user_pw,
            'user_name' => $user_name,
        ];
        $insert_query = "
            insert into sma_user_m (co, user_id, user_pw, user_name)
                values ('C99', :user_id, :user_pw, :user_name)
        ";
        $this->query($bindings, $insert_query);
        
        /** act */
        Auth::loginUsingId($user_id, false);
        $this->target->logout();
        $actual = Auth::check();

        /** assert */
        $this->assertFalse($actual);
    }

    public function testGetMenu()
    {
        /** arrange */
        $user_id = str_random(8);
        $user_pw = str_random(10);
        $user_name = str_random(10);
        $sys_id = str_random(3);
        $prg_id = str_random(8);
        $route = str_random(10);
        $this->insertPrgInfo($user_id, $user_pw, $user_name, $sys_id, $prg_id, $route); 
        $menu = [
            ['sys_id' => $sys_id, 
                'sys_name' => $sys_id,
                'prg_id' => $prg_id,
                'prg_name' => $prg_id,
                'user_id' => $user_id,],
        ];
        $expected = ['result' => true, 'msg' => '已取得清單!(#0000)', 'menu' => $menu];

        /** act */
        $actual = $this->target->getMenu($user_id);

        /** assert */
        $this->assertEquals($expected, $actual);
    }

    private function insertPrgInfo($user_id, $user_pw, $user_name, $sys_id, $prg_id, $route)
    {
        $bindings = [
            'user_id' => $user_id,
            'user_pw' => $user_pw,
            'user_name' => $user_name];
        $insert_query = "insert into sma_user_m (co, user_id, user_pw, user_name)
                            values ('C99', :user_id, :user_pw, :user_name)";
        $this->query($bindings, $insert_query);

        $bindings = ['sys_id' => $sys_id];
        $insert_query = "insert into sma_sys_prg_m (co, sys_id, sys_name)
                            values ('C99', :sys_id, :sys_id)";
        $this->query($bindings, $insert_query);

        $bindings = ['sys_id' => $sys_id, 'prg_id' => $prg_id];
        $insert_query = "insert into sma_sys_prg_d (co, sys_id, prg_id, prg_name)
                            values ('C99', :sys_id, :prg_id, :prg_id)";
        $this->query($bindings, $insert_query);

        $bindings = ['prg_id' => $prg_id, 'route' => $route];
        $insert_query = "insert into api_web_prg (co, prg_id, web_route)
                            values ('C99', :prg_id, :route)";
        $this->query($bindings, $insert_query);

        $bindings = ['user_id' => 'S'.$user_id, 'prg_id' => $prg_id.' '.str_random(8)];
        $insert_query = "insert into sma_tree (co, user_id, class, data_d)
                            values ('C99', :user_id, 'SYS', :prg_id)";
        $this->query($bindings, $insert_query);
    }
}
