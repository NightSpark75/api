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
     * test getShippingInfo()
     */
    public function test_getShippingInfo()
    {
        // arrange
        $expected = response()->json([], 200);
        $date = null;
        $spno = '123456';

        // act
        $this->mock->shouldReceive('getShippingInfo')
            ->once()
            ->with($spno, $date)
            ->andReturn($expected);
        $actual = $this->target->getShippingInfo($spno, $date);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    /**
     * test getShippingInfo exception
     */
    public function test_getShippingInfo_exception()
    {
        //arrange
        $expected = response()->json([], 400);
        $date = null;
        $spno = 'test';

        //act
        $this->mock->shouldReceive('getShippingInfo')
            ->once()
            ->with($spno, $date)
            ->andThrow(new Exception());

        $actual = $this->target->getShippingInfo($spno, $date);
        //assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    /**
     * test savePieces
     */
    public function test_savePieces()
    {   
        //arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $spno = '';
        $date = '';
        $pieces = '';
        $expected = response()->json(['result' => true], 200);

        //act
        $this->mock->shouldReceive('savePieces')
            ->once()
            ->with($spno, $date, $user->id, $pieces);
        $actual = $this->target->savePieces();
        //assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }

    /**
     * test savePieces exception
     */
    public function test_savePieces_exception()
    {
        //arrange
        $user = User::first();
        $this->session(['user' => $user]);
        $spno = '';
        $date = '';
        $pieces = '';
        $expected = response()->json([], 400);

        //act
        $this->mock->shouldReceive('savePieces')
            ->once()
            ->with($spno, $date, $user->id, $pieces)
            ->andThrow(New Exception('test'));
        try {
            $actual = $this->target->savePieces();
        } catch (Exception $e) {
            $actual = $this->target->savePieces();
        }

        //assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }
}
