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
        $date = $expected->tmaddj;
        $spno = $expected->tmy59spno;

        // act
        $this->mock->shouldReceive('getShippingInfo')
            ->once()
            ->with($spno, $date)
            ->andReturn($expected);
        $actual = $this->target->getShippingInfo($spno, $date);

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test getShippingInfo(), throw no data found Exception 
     */
    public function test_getShippingInfo_exception()
    {
        // arrange
        $spno = '1234';
        $date = '20180321';
        $expected = "查貨號 = $spno, 日期 = $date, 查詢不到資料";

        // act
        $this->mock->shouldReceive('getShippingInfo')
            ->once()
            ->with($spno, $date)
            ->andReturn($expected);

        try {
            $actual = $this->target->getShippingInfo($spno, $date);
        } catch (Exception $e) {
            $exception = $e->getMessage();
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
            select('tmtrdj', 'tmaddj', 'tmy59spno', 'tmcars', 'cars_na', 'tman8', 'tmalph', 'tm1in1', 'dltm_na', 'tmalph1')
            ->first();
        $spno = $data->tmy59spno;
        $date = $data->tmaddj;
        $user = '50001';
        $pieces = '20';

        //act
        $this->mock->shouldReceive('getShippingInfo')
            ->once()
            ->with($spno, $date . ' 00:00:00')
            ->andReturn($data);

        //assert

    }
}
