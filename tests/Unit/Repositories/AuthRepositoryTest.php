<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Repositories\AuthREpository;
use App\Traits\Sqlexecute;
use Auth;


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
                values ('C01', :user_id, :user_pw, :user_name, 'Y')
        ";
        $this->query($bindings, $insert_query);
        
        /** act */
        $actual = $this->target->login($user_id, $user_pw, $sys);
        $actual_user = \Auth::check();

        /** assert */
        $this->assertEquals($expected, $actual);
        $this->assertTrue($actual_user);
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
                values ('TTT', :user_id, :user_pw, :user_name)
        ";
        $this->query($bindings, $insert_query);
        
        /** act */
        Auth::loginUsingId($user_id, false);
        $this->target->logout();
        $actual = Auth::check();
        /** assert */
        $this->assertFalse($actual);
    }
}
