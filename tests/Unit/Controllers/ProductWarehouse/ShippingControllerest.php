<?php
/**
 * ShippingController unit test
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

use App\Http\Controllers\ProductWarehouse\ShippingController;
use App\Services\ProductWarehouse\ShippingService;
use Exception;
use App\Models\Web\User;

/**
 * Class ShippingControllerTest
 *
 * @package Tests\Unit\Controllers
 */
class ShippingControllersTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;
    
    /**
     * @var ShippingService
     */
    private $mock;
    
    /**
     * @var ShippingController
     */
    private $target;

    /**
     * setUp()
     */
    public function setUp()
    {
        parent::setUp();
        $this->mock = $this->initMock(ShippingService::class);
        $this->target = $this->app->make(ShippingController::class);
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
     * test getShippingList()
     */
    public function test_getShippingList()
    {
        // arrange
        $expected = response()->json([], 200);
        $date = null;

        // act
        $this->mock->shouldReceive('getTodayShippingList')
            ->once()
            ->with($date)
            ->andReturn($expected);
        $actual = $this->target->getShippingList();

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    /**
     * tes getShippingList() return exception 
     */
    public function test_getShippingList_exception()
    {
        // arrange
        $expected = response()->json([], 400);
        $date = null;

        // act
        $this->mock->shouldReceive('getTodayShippingList')
            ->once()
            ->with($date)
            ->andThrow(new Exception());
        $actual = $this->target->getShippingList();

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    /**
     * test getShippingList()
     */
    public function test_getShippingItems()
    {
        // arrange
        $stop = request()->input('stop');
        $date = null;
        $expected = response()->json([], 200);

        // act
        $this->mock->shouldReceive('getShippingItems')
            ->once()
            ->with($stop, $date)
            ->andReturn($expected);
        $actual = $this->target->getShippingItems($stop);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    /**
     * tes getShippingList() return exception 
     */
    public function test_getShippingItems_exception()
    {
        // arrange
        $stop = request()->input('stop');
        $date = null;
        $expected = response()->json([], 400);

        // act
        $this->mock->shouldReceive('getShippingItems')
            ->once()
            ->with($stop, $date)
            ->andThrow(new Exception());
        $actual = $this->target->getShippingItems($stop);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    /**
     * test startShipping()
     */
    public function test_startShipping()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $stop = request()->input('stop');
        $id = $user->id;
        $date = null;
        $expected = response()->json([], 200);

        // act
        $this->mock->shouldReceive('startShipping')
            ->once()
            ->with($stop, $id, $date)
            ->andReturn($expected);
        $actual = $this->target->startShipping();
        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
        $this->flushSession();
    }

    /**
     * tes startShipping() return exception 
     */
    public function test_startShipping_exception()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $stop = request()->input('stop');
        $id = $user->id;
        $date = null;
        $expected = response()->json([], 400);

        // act
        $this->mock->shouldReceive('startShipping')
            ->once()
            ->with($stop, $id, $date)
            ->andThrow(new Exception());
        $actual = $this->target->startShipping($stop);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
        $this->flushSession();
    }

    /**
     * test endShipping()
     */
    public function test_endShipping()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $stop = request()->input('stop');
        $id = $user->id;
        $date = null;
        $expected = response()->json([], 200);

        // act
        $this->mock->shouldReceive('endShipping')
            ->once()
            ->with($stop, $id, $date)
            ->andReturn($expected);
        $actual = $this->target->endShipping();
        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
        $this->flushSession();
    }

    /**
     * tes endShipping() return exception 
     */
    public function test_endShipping_exception()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $stop = request()->input('stop');
        $id = $user->id;
        $date = null;
        $expected = response()->json([], 400);

        // act
        $this->mock->shouldReceive('endShipping')
            ->once()
            ->with($stop, $id, $date)
            ->andThrow(new Exception());
        $actual = $this->target->endShipping($stop);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
        $this->flushSession();
    }
}
