<?php
/**
 * ShippingService unit test
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
use App\Services\ProductWarehouse\ShippingService;
use App\Repositories\ProductWarehouse\ShippingListRepository;
use App\Repositories\ProductWarehouse\ShippingItemsRepository;
use App\Models\ProductWarehouse\ShippingList;

/**
 * Class ShippingServiceTest
 *
 * @package Tests\Unit\Services\ProductWarehouse
 */
class ShippingServiceTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;
    
    /**
     * @var ShippingListRepository
     */
    private $mock;
    
    /**
     * @var ShippingService
     */
    private $target;

    /**
     * setUp()
     */
    public function setUp()
    {
        parent::setUp();
        $this->mock = $this->initMock(ShippingListRepository::class);
        $this->target = $this->app->make(ShippingService::class);
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
        $expected = ShippingList::select('tmtrdj', 'tmaddj', 'tmy59spno', 'tmcars', 'cars_na', 'tman8', 'tmalph', 'tm1in1', 'dltm_na', 'tmalph1')->first();
        $spno = $expected->tmy59spno;

        // act
        $this->mock->shouldReceive('getShippingInfo')
            ->once()
            ->with($spno)
            ->andReturn($expected);
        $actual = $this->target->getShippingInfo($spno);

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test getShippingInfo(), throw no data found Exception 
     */
    public function test_getShippingInfo_exception()
    {
        // arrange
        $data = ShippingList::
            where('tmy59spno', 'test')
            ->where('tmaddj', null)
            ->select('tmtrdj', 'tmaddj', 'tmy59spno', 'tmcars', 'cars_na', 'tman8', 'tmalph', 'tm1in1', 'dltm_na', 'tmalph1')
            ->first();
        $spno = '1234';
        $expected = "查貨號 = $spno, 查詢不到資料";

        // act
        $this->mock->shouldReceive('getShippingInfo')
            ->once()
            ->with($spno)
            ->andReturn($data);
        try {
            $actual = $this->target->getShippingInfo($spno);
        } catch (Exception $e) {
            $actual = $e->getMessage();
        }

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test savePieces
     */
    public function test_savePieces()
    {
        //arrange
        $data = ShippingList::
            //where('tmaddj', null)
            select('tmtrdj', 'tmaddj', 'tmy59spno', 'tmcars', 'cars_na', 'tman8', 'tmalph', 'tm1in1', 'dltm_na', 'tmalph1')
            ->first();
        $spno = $data->tmy59spno;
        $date = date_format(date_create($data->tmtrdj), 'Y/m/d') . ' 00:00:00';
        $user = '50001';
        $pieces = '20';

        //act
        $this->mock->shouldReceive('checkShippingInfo')
            ->once()
            ->with($spno, $date)
            ->andReturn(1);

        $this->mock->shouldReceive('savePieces')
            ->once()
            ->with($spno, $date, $user, $pieces);

        $actual = $this->target->savePieces($spno, $date, $user, $pieces);

        //assert
        $this->assertTrue($actual);
    }

    /**
     * test savePirces exception
     */
    public function test_savePieces_exception()
    {
        //arrange
        $data = ShippingList::
            where('tmy59spno', 'test')
            ->where('tmaddj', null)
            ->select('tmtrdj', 'tmaddj', 'tmy59spno', 'tmcars', 'cars_na', 'tman8', 'tmalph', 'tm1in1', 'dltm_na', 'tmalph1')
            ->first();

        $spno = 'test';
        $date = '1911-01-01 00:00:00';
        $user = '50001';
        $pieces = '20';
        $expected = "spno='$spno', data not found!";

        //act
        $this->mock->shouldReceive('checkShippingInfo')
            ->once()
            ->with($spno, $date)
            ->andReturn(0);
        
        $this->mock->shouldReceive('savePieces')
            ->times(0)
            ->with($spno, $date, $user, $pieces);

        try {
            $actual = $this->target->savePieces($spno, $date, $user, $pieces);
        } catch (Exception $e) {
            $actual = $e->getMessage();
        }

        //assert
        $this->assertEquals($expected, $actual);
    }
}
