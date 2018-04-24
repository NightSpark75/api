<?php
/**
 * InventorygController unit test
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/04/24
 * @since 1.0.0 spark: complete test
 * 
 */
namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Controllers\ProductWarehouse\InventoryController;
use App\Services\ProductWarehouse\InventoryService;
use App\Services\Web\ExcelService;
use Exception;
use App\Models\Web\User;
use Auth;

/**
 * Class InventoryControllerTest
 *
 * @package Tests\Unit\Controllers
 */
class InventoryControllersTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;
    
    /**
     * @var InventoryService
     */
    private $mock;
    
    /**
     * @var InventoryController
     */
    private $target;

    /**
     * setUp()
     */
    public function setUp()
    {
        parent::setUp();
        $this->mock = $this->initMock(InventoryService::class);
        $this->target = $this->app->make(InventoryController::class);
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
    
    public function test_getInventoryList()
    {
        // arrange
        $date = '20180423';
        $list = [];
        $expected = response()->json($list, 200);

        // act
        $this->mock->shouldReceive('getInventoryList')
            ->once()
            ->with($date)
            ->andReturn($list);
        $actual = $this->target->getInventoryList($date);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    public function test_getInventoryList_exception()
    {
        // arrange
        $date = '20180423';
        $expected = response()->json([], 400);

        // act
        $this->mock->shouldReceive('getInventoryList')
            ->once()
            ->with($date)
            ->andThrow(new Exception());
        $actual = $this->target->getInventoryList($date);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    public function test_checkFinished()
    {
        // arrange
        $cyno = '10000748';
        $finished = true;
        $expected = response()->json($finished, 200);

        // act
        $this->mock->shouldReceive('checkFinished')
            ->once()
            ->with($cyno)
            ->andReturn($finished);
        $actual = $this->target->checkFinished($cyno);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    public function test_checkFinished_exception()
    {
        // arrange
        $cyno = '10000748';
        $finished = false;
        $expected = response()->json($finished, 400);

        // act
        $this->mock->shouldReceive('checkFinished')
            ->once()
            ->with($cyno)
            ->andThrow(new Exception());
        $actual = $this->target->checkFinished($cyno);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    public function test_getInventoryItem()
    {
        // arrange
        $cyno = '10000748';
        $item = new \StdClass();
        $expected = response()->json($item, 200);

        // act
        $this->mock->shouldReceive('getInventoryItem')
            ->once()
            ->with($cyno)
            ->andReturn($item);
        $actual = $this->target->getInventoryItem($cyno);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    public function test_getInventoryItem_exception()
    {
        // arrange
        $cyno = '10000748';
        $item = new \StdClass();
        $expected = response()->json($item, 400);

        // act
        $this->mock->shouldReceive('getInventoryItem')
            ->once()
            ->with($cyno)
            ->andThrow(new Exception());
        $actual = $this->target->getInventoryItem($cyno);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    public function test_saveInventory()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $id = $user->id;
        $cyno = '';
        $locn = '';
        $litm = '';
        $lotn = '';
        $amount = 0;
        $item = new \StdClass();
        $expected = response()->json($item, 200);

        // act
        $this->mock->shouldReceive('saveInventory')
            ->once()
            ->with($id, $cyno, $locn, $litm, $lotn, $amount)
            ->andReturn($item);
        $actual = $this->target->saveInventory();

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    public function test_saveInventory_exception()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $id = $user->id;
        $cyno = '';
        $locn = '';
        $litm = '';
        $lotn = '';
        $amount = 0;
        $item = new \StdClass();
        $expected = response()->json($item, 400);

        // act
        $this->mock->shouldReceive('saveInventory')
            ->once()
            ->with($id, $cyno, $locn, $litm, $lotn, $amount)
            ->andThrow(new Exception());
        $actual = $this->target->saveInventory();

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    public function test_inventoried()
    {
        // arrange
        $id = User::first()->id;
        Auth::loginUsingId($id, false);
        $cyno = '10000748';
        $inventoried = [[], []];
        $expected = response()->json(['inventoried' => $inventoried], 200);

        // act
        $this->mock->shouldReceive('getInventoried')
            ->once()
            ->with($id, $cyno)
            ->andReturn($inventoried);
        $actual = $this->target->inventoried($cyno);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    public function test_inventoried_no_data()
    {
        // arrange
        $id = User::first()->id;
        Auth::loginUsingId($id, false);
        $cyno = '10000748';
        $inventoried = [];
        $expected = response()->json(['msg' => '無盤點資料!'], 401);

        // act
        $this->mock->shouldReceive('getInventoried')
            ->once()
            ->with($id, $cyno)
            ->andReturn($inventoried);
        $actual = $this->target->inventoried($cyno);


        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    public function test_inventoried_exception()
    {
        // arrange
        $id = User::first()->id;
        Auth::loginUsingId($id, false);
        $cyno = '10000748';
        $inventoried = [];
        $expected = response()->json($inventoried, 400);

        // act
        $this->mock->shouldReceive('getInventoried')
            ->once()
            ->with($id, $cyno)
            ->andThrow(new Exception());
        $actual = $this->target->inventoried($cyno);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    public function test_export()
    {
        // arrange
        $id = User::first()->id;
        Auth::loginUsingId($id, false);
        $cyno = '10000748';
        $inventoried = [];
        $expected = '';

        // act
        $this->mock->shouldReceive('getExportData')
            ->once()
            ->with($id, $cyno)
            ->andReturn($inventoried);
        $this->mock = $this->initMock(ExcelService::class);
        $this->target = $this->app->make(InventoryController::class);
        $this->mock->shouldReceive('download')
            ->once()
            ->with($inventoried, $cyno.'盤點資料.xlsx', true)
            ->andReturn($expected);
        $actual = $this->target->export($cyno);

        // assert
        $this->assertEquals($expected, $actual);
    }
}
