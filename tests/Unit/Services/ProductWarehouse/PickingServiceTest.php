<?php
/**
 * PickingService unit test
 *
 * @version 1.0.2
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/03/20
 * @since 1.0.0 spark: complete 6 test
 * @since 1.0.2 spark: completed unit test and optimized code
 * 
 */
namespace Tests\Unit\Services\ProductWarehouse;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use DB;
use Exception;
use App\Services\ProductWarehouse\PickingService;
use App\Repositories\ProductWarehouse\PickingListRepository;
use App\Repositories\ProductWarehouse\PickingItemsRepository;
use App\Models\ProductWarehouse\PickingList;
use App\Models\ProductWarehouse\PickingItems;

/**
 * Class PickingServiceTest
 *
 * @package Tests\Unit\Services\ProductWarehouse
 */
class PickingServiceTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;
    
    /**
     * @var PickingListRepository
     */
    private $mock;
    
    /**
     * @var PickingService
     */
    private $target;

    /**
     * setUp()
     */
    public function setUp()
    {
        parent::setUp();
        $this->mock = $this->initMock(PickingListRepository::class);
        $this->target = $this->app->make(PickingService::class);
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
     * test getPickingList()
     */
    public function test_getPickingList()
    {
        // arrange
        $user = str_random(6);
        $date = '20180426';
        $current = [];
        $list = ['1', '2'];
        $expected = compact('current', 'list');

        // act
        $this->mock->shouldReceive('getCurrent')
            ->once()
            ->with($user, $date)
            ->andReturn($current);

        $this->mock->shouldReceive('getPickingList')
            ->once()
            ->with($user, $date)
            ->andReturn($list);
        $actual = $this->target->getPickingList($user, $date);

        // assert
        $this->assertEquals($expected, $actual);
    }

    public function test_getPickingList_current()
    {
        // arrange
        $user = str_random(6);
        $date = '20180426';
        $current = new \stdClass();
        $current->test = str_random(4);
        $list = [];
        $expected = compact('current', 'list');

        // act
        $this->mock->shouldReceive('getCurrent')
            ->once()
            ->with($user, $date)
            ->andReturn($current);

        $this->mock->shouldReceive('getPickingList')
            ->times(0)
            ->with($user, $date);
        $actual = $this->target->getPickingList($user, $date);

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test startPicking() 
     * call procedure proc_upd_f594921 and check the data is updated
     */
    public function test_startPicking()
    {
        // arrange
        $date = '20180426';
        $stop = str_random(3);
        $user = str_random(6);
        $check = true;
        $staddj = date_format(date_create($date), 'Y/m/d');

        // act
        $this->mock->shouldReceive('checkStartPicking')
            ->once()
            ->with($stop, $date)
            ->andReturn($check);
        
        $this->mock->shouldReceive('startPicking')
            ->once()
            ->with($stop, $staddj, $user);
        
        $actual = $this->target->startPicking($stop, $user, $date);

        // assert
        $this->assertTrue($actual);
    }

    /**
     * test startPicking(), throw no data found Exception 
     */
    public function test_startPicking_exception()
    {
        // arrange
        $date = '20180426';
        $stop = str_random(3);
        $user = str_random(6);
        $check = false;
        $staddj = date_format(date_create($date), 'Y/m/d');
        $expected = "ststop='$stop' and staddj='$date', data not found!";

        // act
        $this->mock->shouldReceive('checkStartPicking')
            ->once()
            ->with($stop, $date)
            ->andReturn($check);
        
        $this->mock->shouldReceive('startPicking')
            ->times(0)
            ->with($stop, $staddj, $user);
        
        try {
            $actual = $this->target->startPicking($stop, $user, $date);
        } catch (Exception $e) {
            $actual = $e->getMessage();
        }

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test endPicking() 
     * call procedure proc_upd_f594921 and check the data is updated
     */
    public function test_endPicking()
    {
        // arrange
        $date = '20180426';
        $stop = str_random(3);
        $user = str_random(6);
        $check = true;
        $staddj = date_format(date_create($date), 'Y/m/d');

        // act
        $this->mock->shouldReceive('checkPicking')
            ->once()
            ->with($stop, $date, $user)
            ->andReturn($check);
        
        $this->mock->shouldReceive('endPicking')
            ->once()
            ->with($stop, $staddj, $user);
        
        $actual = $this->target->endPicking($stop, $user, $date);

        // assert
        $this->assertTrue($actual);
    }


    /**
     * tes endPicking(), throw no data found Exception 
     */
    public function test_endPicking_exception()
    {
        // arrange
        $date = '20180426';
        $stop = str_random(3);
        $user = str_random(6);
        $check = false;
        $staddj = date_format(date_create($date), 'Y/m/d');
        $expected = "ststop='$stop' and staddj='$date', data not found!";

        // act
        $this->mock->shouldReceive('checkPicking')
            ->once()
            ->with($stop, $date, $user)
            ->andReturn($check);
        
        $this->mock->shouldReceive('endPicking')
            ->times(0)
            ->with($stop, $staddj, $user);
        
        try {
            $actual = $this->target->endPicking($stop, $user, $date);
        } catch (Exception $e) {
            $actual = $e->getMessage();
        }

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test getPickingItem() 
     */
    public function test_getPickingItem()
    {
        // arrange
        $this->mock = $this->initMock(PickingItemsRepository::class);
        $this->target = $this->app->make(PickingService::class);
        $user = str_random(6);
        $stop = str_random(3);
        $date = '20180426';
        $return = ['a', 'b'];
        $expected = 'a';

        // act
        $this->mock->shouldReceive('getPickingItems')
            ->times(1)
            ->with(trim($stop), $date, $user)
            ->andReturn($return);
        $actual = $this->target->getPickingItem(trim($stop), $user, $date);

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test getPickingItems() 
     */
    public function test_getPickingItems()
    {
        // arrange
        $this->mock = $this->initMock(PickingItemsRepository::class);
        $this->target = $this->app->make(PickingService::class);
        $stop = str_random(3);
        $date = '20180426';
        $expected =  ['a', 'b'];

        // act
        $this->mock->shouldReceive('getPickingItems')
            ->times(1)
            ->with(trim($stop), $date)
            ->andReturn($expected);
        $actual = $this->target->getPickingItems(trim($stop), $date);

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test pausePicking
     */
    public function test_pausePicking()
    {
        // arrange
        $date = '20180426';
        $stop = str_random(3);
        $user = str_random(6);
        $check = true;
        $staddj = date_format(date_create($date), 'Y/m/d');

        // act
        $this->mock->shouldReceive('checkPicking')
            ->once()
            ->with($stop, $date, $user)
            ->andReturn($check);
        
        $this->mock->shouldReceive('pausePicking')
            ->once()
            ->with($stop, $staddj, $user);
        
        $actual = $this->target->pausePicking($stop, $date, $user);

        // assert
        $this->assertTrue($actual);
    }

    /**
     * test pausePicking exception
     */
    public function test_pausePicking_exception()
    {
        // arrange
        $date = '20180426';
        $stop = str_random(3);
        $user = str_random(6);
        $check = false;
        $staddj = date_format(date_create($date), 'Y/m/d');
        $expected = "ststop='$stop' and staddj='$date', data not found!";

        // act
        $this->mock->shouldReceive('checkPicking')
            ->once()
            ->with($stop, $date, $user)
            ->andReturn($check);
        
        $this->mock->shouldReceive('pausePicking')
            ->times(0)
            ->with($stop, $staddj, $user);
        
        try {
            $actual = $this->target->pausePicking($stop, $date, $user);
        } catch (Exception $e) {
            $actual = $e->getMessage();
        }

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test pickup
     */
    public function test_pickup()
    {
        // arrange
        $date = '20180426';
        $check = true;
        $stop = str_random(3);
        $rmk = str_random(5);
        $litm = str_random(5);
        $lotn = str_random(5);
        $user = str_random(6);
        $staddj = date_format(date_create($date), 'Y/m/d');
        $item = ['a', 'b', 'c'];
        $expected = 'a';

        // act
        $this->mock->shouldReceive('checkPicking')
            ->once()
            ->with($stop, $date, $user)
            ->andReturn($check);

        $this->mock = $this->initMock(PickingItemsRepository::class);
        $this->target = $this->app->make(PickingService::class);
        $this->mock->shouldReceive('pickup')
            ->once()
            ->with($stop, $staddj, $rmk, $litm, $lotn, $user);

        $this->mock->shouldReceive('getPickingItems')
            ->once()
            ->with($stop, $date, $user)
            ->andReturn($item);

        $actual = $this->target->pickup($stop, $date, $rmk, $litm, $lotn, $user);

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test pickup exception
     */
    public function test_pickup_exception()
    {
        // arrange
        $date = '20180426';
        $check = false;
        $stop = str_random(3);
        $rmk = str_random(5);
        $litm = str_random(5);
        $lotn = str_random(5);
        $user = str_random(6);
        $staddj = date_format(date_create($date), 'Y/m/d');
        $expected = "
            ststop='$stop' and staddj='$date' 
            and rmk = '$rmk' and litm = '$litm' and lotn = '$lotn' 
            , data not found!
        ";

        // act
        $this->mock->shouldReceive('checkPicking')
            ->once()
            ->with($stop, $date, $user)
            ->andReturn($check);

        $this->mock = $this->initMock(PickingItemsRepository::class);
        $this->target = $this->app->make(PickingService::class);
        $this->mock->shouldReceive('pickup')
            ->times(0)
            ->with($stop, $staddj, $rmk, $litm, $lotn, $user);

        $this->mock->shouldReceive('getPickingItems')
            ->times(0)
            ->with($stop, $date, $user);

        try {
            $actual = $this->target->pickup($stop, $date, $rmk, $litm, $lotn, $user);
        } catch (Exception $e) {
            $actual = $e->getMessage();
        }

        // assert
        $this->assertEquals($expected, $actual);
    }
}
