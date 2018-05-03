<?php
/**
 * PickingListRepository unit test
 *
 * @version 1.0.2
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/03/20
 * @since 1.0.0 spark: complete 4 test
 * @since 1.0.2 spark: completed unit test and optimized code
 * 
 */
namespace Tests\Unit\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use DB;
use Exception;
use App\Models\ProductWarehouse\PickingList;
use App\Repositories\ProductWarehouse\PickingListRepository;

/**
 * Class PickingListRepositoryTest
 *
 * @package Tests\Unit\Repositories
 */
class PickingListRepositoryTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;
    
    /**
     * @var PickingListRepository
     */
    private $target;

    /**
     * setUp()
     */
    public function setUp()
    {
        parent::setUp();
        $this->target = $this->app->make(PickingListRepository::class);
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

    public function test_checkCurrent()
    {
        // arrange
        $user = str_random(5);
        $date = '20180503';
        $picking = DB::selectOne("
            select trim(j.ststop) ststop
                from jdv_f594921 j
                where staddj = to_date('$date', 'YYYYMMDD')
        ");
        $ststop = $picking->ststop;      
        $expected = 1;

        // act
        DB::insert("
            insert into mpm_picking_m
                values ('$ststop', $date, sysdate, null, 'Y', '$user', sysdate)
        ");
        $actual = $this->target->getCurrent($user, $date);

        // assert
        $this->assertEquals($expected, count($actual));
    }
    
    /**
     * test getPickingList()
     */
    public function test_getPickingList()
    {
        // arrange
        $date = '20180503';
        $user = str_random(5);
        $picking = DB::selectOne("
            select trim(j.ststop) ststop
                from jdv_f594921 j
                where staddj = to_date('$date', 'YYYYMMDD')
        ");
        $ststop = $picking->ststop;      
        $expected = count($picking) - 1;
        
        // act
        DB::insert("
            insert into mpm_picking_m
                values ('$ststop', $date, sysdate, null, 'Y', '$user', sysdate)
        ");
        $actual = $this->target->getPickingList($user, $date);

        // assert
        $this->assertEquals($expected, count($actual));
    }

    public function test_checkStartPicking_true()
    {
        // arrange
        $user = str_random(5);
        $date = '20180503';
        $ststop = DB::selectOne("
            select trim(j.ststop) ststop
                from jdv_f594921 j
                where staddj = to_date('$date', 'YYYYMMDD')
        ")->ststop;       

        // act
        DB::delete("
            delete mpm_picking_m where stop = '$ststop'
        ");
        $actual = $this->target->checkStartPicking($ststop, $date);

        // assert
        $this->assertTrue($actual);
    }

    public function test_checkStartPicking_false()
    {
        // arrange
        $user = str_random(5);
        $date = '20180503';
        $ststop = DB::selectOne("
            select trim(j.ststop) ststop
                from jdv_f594921 j
                where staddj = to_date('$date', 'YYYYMMDD')
        ")->ststop;         

        // act
        DB::insert("
            insert into mpm_picking_m
                values ('$ststop', $date, sysdate, null, 'Y', '$user', sysdate)
        ");
        $actual = $this->target->checkStartPicking($ststop, $date);

        // assert
        $this->assertFalse($actual);
    }

    public function test_checkPicking_true()
    {
        // arrange
        $user = str_random(5);
        $date = '20180503';
        $ststop = DB::selectOne("
            select trim(j.ststop) ststop
                from jdv_f594921 j
                where stky6 is null and staddj = to_date('$date', 'YYYYMMDD')
        ")->ststop;        

        // act
        DB::insert("
            insert into mpm_picking_m
                values ('$ststop', $date, sysdate, null, 'Y', '$user', sysdate)
        ");
        $actual = $this->target->checkPicking($ststop, $date, $user);

        // assert
        $this->assertTrue($actual);
    }

    public function test_checkPicking_false()
    {
        // arrange
        $user = str_random(5);
        $date = '20180503';
        $ststop = DB::selectOne("
            select trim(j.ststop) ststop
                from jdv_f594921 j
                where stky6 is null and staddj = to_date('$date', 'YYYYMMDD')
        ")->ststop;   

        // act
        DB::delete("
            delete mpm_picking_m where stop = '$ststop'
        ");
        $actual = $this->target->checkPicking($ststop, $date, $user);

        // assert
        $this->assertFalse($actual);
    }

    public function test_startPicking()
    {
        // arrange
        $date = '20180503';
        $stop = str_random(4);
        $user = str_random(5);
        $expected = $stop;

        // act
        $actual = $this->target->startPicking($stop, $date, $user);
        $actual2 = DB::selectOne("
            select * from mpm_picking_m
                where stop = '$stop'
                    and addj = $date
                    and state = 'Y'
        ")->stop;    

        // assert
        $this->assertTrue($actual);
        $this->assertEquals($expected, $actual2);
    }

    public function test_pausePicking()
    {
        // arrange
        $date = '20180503';
        $stop = str_random(4);
        $user = str_random(5);
        $expected = $stop;

        // act
        DB::insert("
            insert into mpm_picking_m
                values ('$stop', '$date', sysdate, null, 'Y', '$user', sysdate)
        ");
        $actual = $this->target->pausePicking($stop, $date, $user);
        $actual2 = DB::selectOne("
            select * from mpm_picking_m 
                where stop = '$stop' 
                    and duser = '$user'
                    and state = 'P'
        ")->stop;    
        // assert
        $this->assertTrue($actual);
        $this->assertEquals($expected, $actual2);
    }

    public function test_endPicking()
    {
        // arrange
        $date = '20180503';
        $stop = str_random(4);
        $user = str_random(5);
        $expected = $stop;

        // act
        DB::insert("
            insert into mpm_picking_m
                values ('$stop', '$date', sysdate, null, 'Y', '$user', sysdate)
        ");
        $actual = $this->target->endPicking($stop, $date, $user);
        $actual2 = DB::selectOne("
            select * from mpm_picking_m 
                where stop = '$stop' 
                    and duser = '$user'
                    and state = 'E'
        ")->stop;    
        // assert
        $this->assertTrue($actual);
        $this->assertEquals($expected, $actual2);
    }
}
