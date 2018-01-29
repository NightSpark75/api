<?php
/**
 * PickingController unit test
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/01/29
 * @since 1.0.0 spark: complete 8 test
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
        $expected = response()->json([], 200);

        // act
        $this->mock->shouldReceive('getTodayPickingList')
            ->once()
            ->withNoArgs()
            ->andReturn($expected);
        $actual = $this->target->getPickingList();

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    /**
     * tes getPickingList() return exception 
     */
    public function test_getPickingList_exception()
    {
        // arrange
        $expected = response()->json([], 400);

        // act
        $this->mock->shouldReceive('getTodayPickingList')
            ->once()
            ->withNoArgs()
            ->andThrow(new Exception());
        $actual = $this->target->getPickingList();

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    /**
     * test getPickingList()
     */
    public function test_getPickingItems()
    {
        // arrange
        $stop = request()->input('stop');
        $expected = response()->json([], 200);

        // act
        $this->mock->shouldReceive('getPickingItems')
            ->once()
            ->with($stop)
            ->andReturn($expected);
        $actual = $this->target->getPickingItems($stop);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    /**
     * tes getPickingList() return exception 
     */
    public function test_getPickingItems_exception()
    {
        // arrange
        $stop = request()->input('stop');
        $expected = response()->json([], 400);

        // act
        $this->mock->shouldReceive('getPickingItems')
            ->once()
            ->with($stop)
            ->andThrow(new Exception());
        $actual = $this->target->getPickingItems($stop);

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
        $expected = response()->json([], 200);

        // act
        $this->mock->shouldReceive('startPicking')
            ->once()
            ->with($stop, $id)
            ->andReturn($expected);
        $actual = $this->target->startPicking();
        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
        $this->flushSession();
    }

    /**
     * tes startPicking() return exception 
     */
    public function test_startPicking_exception()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $stop = request()->input('stop');
        $id = $user->id;
        $expected = response()->json([], 400);

        // act
        $this->mock->shouldReceive('startPicking')
            ->once()
            ->with($stop, $id)
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
        $expected = response()->json([], 200);

        // act
        $this->mock->shouldReceive('endPicking')
            ->once()
            ->with($stop, $id)
            ->andReturn($expected);
        $actual = $this->target->endPicking();
        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
        $this->flushSession();
    }

    /**
     * tes endPicking() return exception 
     */
    public function test_endPicking_exception()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $stop = request()->input('stop');
        $id = $user->id;
        $expected = response()->json([], 400);

        // act
        $this->mock->shouldReceive('endPicking')
            ->once()
            ->with($stop, $id)
            ->andThrow(new Exception());
        $actual = $this->target->endPicking($stop);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
        $this->flushSession();
    }
}
