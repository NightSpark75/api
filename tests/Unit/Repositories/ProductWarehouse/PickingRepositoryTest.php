<?php
/**
 * PickingRepository unit test
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/01/29
 * @since 1.0.0 spark: complete six test
 * 
 */
namespace Tests\Unit\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Exception;
use App\Models\ProductWarehouse\PickingList;
use App\Models\Web\User;

/**
 * Class PickingRepositoryTest
 *
 * @package Tests\Unit\Repositories
 */
class PickingRepositoryTest extends TestCase
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
     * test getPickingList()
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
     * tes getPickingList() return exception 
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
}
