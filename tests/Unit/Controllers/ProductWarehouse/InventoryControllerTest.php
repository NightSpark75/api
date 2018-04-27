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
        $user = User::first();
        $this->session(['user' => $user]);
        $id = $user->id;
        $date = '20180423';
        $current = [];
        $list = [];
        $compact = compact($current, $list);
        $expected = response()->json($compact, 200);

        // act
        $this->mock->shouldReceive('getInventoryList')
            ->once()
            ->with($id, $date)
            ->andReturn($compact);
        $actual = $this->target->getInventoryList($date);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    public function test_getInventoryList_exception()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $id = $user->id;
        $date = '20180423';
        $expected = response()->json([], 400);

        // act
        $this->mock->shouldReceive('getInventoryList')
            ->once()
            ->with($id, $date)
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
        $user = User::first();
        $this->session(['user' => $user]);
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
        $user = User::first();
        $this->session(['user' => $user]);
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
        $cyno = str_random(8);
        $locn = str_random(8);
        $litm = str_random(8);
        $lotn = str_random(8);
        $amount = 0;
        request()->merge(['cyno' => $cyno]);
        request()->merge(['locn' => $locn]);
        request()->merge(['litm' => $litm]);
        request()->merge(['lotn' => $lotn]);
        request()->merge(['amount' => $amount]);
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
        $cyno = str_random(8);
        $locn = str_random(8);
        $litm = str_random(8);
        $lotn = str_random(8);
        $amount = 0;
        request()->merge(['cyno' => $cyno]);
        request()->merge(['locn' => $locn]);
        request()->merge(['litm' => $litm]);
        request()->merge(['lotn' => $lotn]);
        request()->merge(['amount' => $amount]);
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

    public function test_startInventory()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $id = $user->id;
        $cyno = str_random(8);
        $expected = response()->json([], 200);
        request()->merge(['cyno' => $cyno]);

        // act
        $this->mock->shouldReceive('startInventory')
            ->once()
            ->with($cyno, $id);
        $actual = $this->target->startInventory();

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    public function test_startInventory_exception()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $id = $user->id;
        $cyno = str_random(8);
        request()->merge(['cyno' => $cyno]);
        $expected = response()->json([], 400);

        // act
        $this->mock->shouldReceive('startInventory')
            ->once()
            ->with($cyno, $id)
            ->andThrow(new Exception());
        $actual = $this->target->startInventory();

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    public function test_pauseInventory()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $id = $user->id;
        $cyno = str_random(8);
        $expected = response()->json([], 200);
        request()->merge(['cyno' => $cyno]);

        // act
        $this->mock->shouldReceive('pauseInventory')
            ->once()
            ->with($cyno, $id);
        $actual = $this->target->pauseInventory();

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    public function test_pauseInventory_exception()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $id = $user->id;
        $cyno = str_random(8);
        request()->merge(['cyno' => $cyno]);
        $expected = response()->json([], 400);

        // act
        $this->mock->shouldReceive('pauseInventory')
            ->once()
            ->with($cyno, $id)
            ->andThrow(new Exception());
        $actual = $this->target->pauseInventory();

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    public function test_endInventory()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $id = $user->id;
        $cyno = str_random(8);
        $expected = response()->json([], 200);
        request()->merge(['cyno' => $cyno]);

        // act
        $this->mock->shouldReceive('endInventory')
            ->once()
            ->with($cyno, $id);
        $actual = $this->target->endInventory();

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    public function test_endInventory_exception()
    {
        // arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $id = $user->id;
        $cyno = str_random(8);
        request()->merge(['cyno' => $cyno]);
        $expected = response()->json([], 400);

        // act
        $this->mock->shouldReceive('endInventory')
            ->once()
            ->with($cyno, $id)
            ->andThrow(new Exception());
        $actual = $this->target->endInventory();

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }
}
