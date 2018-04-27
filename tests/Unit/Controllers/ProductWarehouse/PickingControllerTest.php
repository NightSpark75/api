<?php
/**
 * PickingController unit test
 *
 * @version 1.0.2
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/03/20
 * @since 1.0.0 spark: complete 8 test
 * @since 1.0.2 spark: completed unit test and optimized code
 * 
 */
namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Controllers\ProductWarehouse\PickingController;
use App\Services\ProductWarehouse\PickingService;
use Exception;
use App\Models\Web\User;

/**
 * Class PickingControllerTest
 *
 * @package Tests\Unit\Controllers
 */
class PickingControllersTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;
    
    /**
     * @var PickingService
     */
    private $mock;
    
    /**
     * @var PickingController
     */
    private $target;

    /**
     * setUp()
     */
    public function setUp()
    {
        parent::setUp();
        $this->mock = $this->initMock(PickingService::class);
        $this->target = $this->app->make(PickingController::class);
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
        $user = User::first();
        $this->session(['user' => $user]);
        $id = $user->id;
        $date = '20180426';
        $result = [];
        $expected = response()->json($result, 200);

        // act
        $this->mock->shouldReceive('getPickingList')
            ->once()
            ->with($id, $date)
            ->andReturn($result);
        $actual = $this->target->getPickingList($date);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    /**
     * test getPickingList() return exception 
     */
    public function test_getPickingList_exception()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $id = $user->id;
        $date = '20180426';
        $expected = response()->json([], 400);

        // act
        $this->mock->shouldReceive('getPickingList')
            ->once()
            ->with($id, $date)
            ->andThrow(new Exception());
        $actual = $this->target->getPickingList($date);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    /**
     * test getPickingList()
     */
    public function test_getPickingItems()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $id = $user->id;
        $stop = str_random(8);
        $date = '20180426';
        $expected = response()->json([], 200);

        // act
        $this->mock->shouldReceive('getPickingItems')
            ->once()
            ->with($stop, $date)
            ->andReturn($expected);
        $actual = $this->target->getPickingItems($stop, $date);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    /**
     * test getPickingList() return exception 
     */
    public function test_getPickingItems_exception()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $id = $user->id;
        $stop = str_random(8);
        $date = '20180426';
        $expected = response()->json([], 400);

        // act
        $this->mock->shouldReceive('getPickingItems')
            ->once()
            ->with($stop, $date)
            ->andThrow(new Exception());
        $actual = $this->target->getPickingItems($stop, $date);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    /**
     * test getPickingItem()
     */
    public function test_getPickingItem()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $id = $user->id;
        $stop = str_random(8);
        $date = '20180426';
        $expected = response()->json([], 200);

        // act
        $this->mock->shouldReceive('getPickingItem')
            ->once()
            ->with($stop, $id, $date)
            ->andReturn($expected);
        $actual = $this->target->getPickingItem($stop, $date);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    /**
     * test gettPickingItem return exception 
     */
    public function test_getPickingItem_exception()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $id = $user->id;
        $stop = str_random(8);
        $date = '20180426';
        $expected = response()->json([], 400);

        // act
        $this->mock->shouldReceive('getPickingItem')
            ->once()
            ->with($stop, $id, $date)
            ->andThrow(new Exception());
        $actual = $this->target->getPickingItem($stop, $date);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    /**
     * test startPicking()
     */
    public function test_startPicking()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $stop = request()->input('stop');
        $id = $user->id;
        $date = null;
        $expected = response()->json([], 200);

        // act
        $this->mock->shouldReceive('startPicking')
            ->once()
            ->with($stop, $id, $date)
            ->andReturn([]);
        $actual = $this->target->startPicking();
        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
        $this->flushSession();
    }

    /**
     * test startPicking() return exception 
     */
    public function test_startPicking_exception()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $stop = request()->input('stop');
        $id = $user->id;
        $date = null;
        $expected = response()->json([], 400);

        // act
        $this->mock->shouldReceive('startPicking')
            ->once()
            ->with($stop, $id, $date)
            ->andThrow(new Exception());
        $actual = $this->target->startPicking($stop);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
        $this->flushSession();
    }

    /**
     * test endPicking()
     */
    public function test_endPicking()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $stop = request()->input('stop');
        $id = $user->id;
        $date = null;
        $expected = response()->json([], 200);

        // act
        $this->mock->shouldReceive('endPicking')
            ->once()
            ->with($stop, $id, $date)
            ->andReturn([]);
        $actual = $this->target->endPicking();
        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
        $this->flushSession();
    }

    /**
     * test endPicking() return exception 
     */
    public function test_endPicking_exception()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $stop = request()->input('stop');
        $id = $user->id;
        $date = null;
        $expected = response()->json([], 400);

        // act
        $this->mock->shouldReceive('endPicking')
            ->once()
            ->with($stop, $id, $date)
            ->andThrow(new Exception());
        $actual = $this->target->endPicking($stop);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
        $this->flushSession();
    }

    /**
     * test pausePicking
     */
    public function test_pausePicking()
    {
        //arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $stop = '';
        $date = '';
        $id = $user->id;
        $expected = response()->json([], 200);

        //act
        $this->mock->shouldReceive('pausePicking')
            ->once()
            ->with($stop, $date, $id)
            ->andReturn([]);
        $actual = $this->target->pausePicking();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test pausePicking exception
     */
    public function test_pausePicking_exception()
    {
        //arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $stop = '';
        $date = '';
        $id = $user->id;
        $expected = response()->json([], 400);

        //act
        $this->mock->shouldReceive('pausePicking')
            ->once()
            ->with($stop, $date, $id)
            ->andThrow(new Exception());
        $actual = $this->target->pausePicking();
        
        //assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    /**
     * test restartPicking 
     */
    public function test_restartPicking()
    {
        //arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $stop = '';
        $date = '';
        $id = $user->id;
        $expected = response()->json([], 200);

        //act
        $this->mock->shouldReceive('restartPicking')
            ->once()
            ->with($stop, $date, $id)
            ->andReturn([]);
        $actual = $this->target->restartPicking();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test restartPicking exception
     */
    public function test_restartPicking_exception()
    {
        //arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $stop = '';
        $date = '';
        $id = $user->id;
        $expected = response()->json([], 400);

        //act
        $this->mock->shouldReceive('restartPicking')
            ->once()
            ->with($stop, $date, $id)
            ->andThrow(new Exception());
        $actual = $this->target->restartPicking();
        
        //assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    /**
     * test pickup
     */
    public function test_pickup()
    {
        //arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $stop = '';
        $date = '';
        $rmk = '';
        $litm = '';
        $lotn = '';
        $id = $user->id;
        $expected = response()->json([], 200);

        //act
        $this->mock->shouldReceive('pickup')
            ->once()
            ->with($stop, $rmk, $litm, $lotn, $date, $id)
            ->andReturn([]);
        $actual = $this->target->pickup();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test pickup exception
     */
    public function test_pickup_exception()
    {
        //arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $stop = '';
        $date = '';
        $rmk = '';
        $litm = '';
        $lotn = '';
        $id = $user->id;
        $expected = response()->json([], 400);

        //act
        $this->mock->shouldReceive('pickup')
            ->once()
            ->with($stop, $rmk, $litm, $lotn, $date, $id)
            ->andThrow(new Exception());
        $actual = $this->target->pickup();
        
        //assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }
}
