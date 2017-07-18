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
    public function test_login()
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
    public function test_login_fail()
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
    public function test_logout()
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

    /**
     * test get menu
     *
     * @return void
     */
    public function test_get_enu()
    {
        /** arrange */
        $user_id = str_random(8);
        $user_pw = str_random(10);
        $user_name = str_random(10);
        $sys_id = str_random(3);
        $prg_id = $sys_id.'F'.str_random(4);
        $route = str_random(10);
        $role_id = str_random(6);
        $this->insertPrgInfo($user_id, $user_pw, $user_name, $sys_id, $prg_id, $route, $role_id); 
        $menu = [
            ['sys_id' => $sys_id, 
                'sys_name' => $sys_id,
                'prg_id' => $prg_id,
                'prg_name' => $prg_id,
                'user_id' => $user_id,
                'web_route' => $route,
                'prg_ins' => 'Y',
                'prg_upd' => 'Y',
                'prg_del' => 'Y',
                'prg_stat' => 'Y'],
        ];
        $expected = ['result' => true, 'msg' => '已取得清單!(#0000)', 'menu' => $menu];

        /** act */
        $actual = $this->target->getMenu($user_id);

        /** assert */
        $this->assertEquals($expected, $actual);
    }

    /**
     * insert program info
     *
     * @return void
     */
    private function insertPrgInfo($user_id, $user_pw, $user_name, $sys_id, $prg_id, $route, $role_id)
    {   
        // insert sma_user_m
        $bindings = [
            'user_id' => $user_id,
            'user_pw' => $user_pw,
            'user_name' => $user_name];
        $insert_query = "insert into sma_user_m (co, user_id, user_pw, user_name)
                            values ('C99', :user_id, :user_pw, :user_name)";
        $this->query($bindings, $insert_query);

        // sma_sys_prg_m
        $bindings = ['sys_id' => $sys_id];
        $insert_query = "insert into sma_sys_prg_m (co, sys_id, sys_name)
                            values ('C99', :sys_id, :sys_id)";
        $this->query($bindings, $insert_query);

        // sma_sys_prg_d
        $bindings = ['sys_id' => $sys_id, 'prg_id' => $prg_id];
        $insert_query = "insert into sma_sys_prg_d (co, sys_id, prg_id, prg_name)
                            values ('C99', :sys_id, :prg_id, :prg_id)";
        $this->query($bindings, $insert_query);

        // insert api_web_prg
        $bindings = ['prg_id' => $prg_id, 'route' => $route];
        $insert_query = "insert into api_web_prg (co, prg_id, web_route)
                            values ('C99', :prg_id, :route)";
        $this->query($bindings, $insert_query);

        // insert sma_tree
        $bindings = ['user_id' => 'S'.$user_id, 'prg_id' => $prg_id.' '.str_random(8)];
        $insert_query = "insert into sma_tree (co, user_id, class, data_d)
                            values ('C99', :user_id, 'SYS', :prg_id)";        
        $this->query($bindings, $insert_query);

        // insert sma_role_prg_m
        $bindings = ['role_id' => $role_id];
        $insert_query = "insert into sma_role_prg_m (co, role_id, role_name, roel_stat)
                            values ('C99', :role_id, :role_id, 'Y')";        
        $this->query($bindings, $insert_query); 

        // insert sma_role_prg_d
        $bindings = ['role_id' => $role_id, 'sys_id' => $sys_id, 'prg_id' => $prg_id];
        $insert_query = "insert into sma_role_prg_d (co, role_id, sys_id, prg_id, prg_ins, prg_upd, prg_del, prg_stat)
                            values ('C99', :role_id, :sys_id, :prg_id, 'Y', 'Y', 'Y', 'Y')";        
        $this->query($bindings, $insert_query); 

        // insert sma_user_role_d
        $bindings = ['user_id' => $user_id, 'role_id' => $role_id];
        $insert_query = "insert into sma_user_role_d (co, user_id, role_id, role_stat)
                            values ('C99', :user_id, :role_id, 'Y')";        
        $this->query($bindings, $insert_query); 

        // insert sma_user_prg_d
        $bindings = ['user_id' => $user_id, 'sys_id' => $sys_id, 'prg_id' => $prg_id];
        $insert_query = "insert into sma_user_prg_d (co, user_id, sys_id, prg_id, prg_ins, prg_upd, prg_del, prg_stat)
                            values ('C99', :user_id, :sys_id, :prg_id, 'Y', 'Y', 'Y', 'Y')";        
        $this->query($bindings, $insert_query); 
    }
}
