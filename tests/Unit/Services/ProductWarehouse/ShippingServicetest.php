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
use App\Models\ProductWarehouse\ShippingItems;

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
        $expected = ShippingList::where('stky6', null)->first();
        $date = $expected->staddj;
        $spno = '';

        // act
        $this->mock->shouldReceive('getShippingInfo')
            ->once()
            ->with($spno, $date)
            ->andReturn($expected);
        $actual = $this->target->getTodayShippingList($spno, $date);

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test getShippingInfo(), throw no data found Exception 
     */
    public function test_getShippingInfo_exception()
    {
        // arrange
        $stop = '1234';
        $$date = '20180321';
        $expected = "查貨號 = $spno, 日期 = $date, 查詢不到資料";

        // act
        $this->mock->shouldReceive('getShippingInfo')
            ->once()
            ->with($stop, $date)
            ->andReturn([]);

        try {
            $actual = $this->target->getShippingInfo($spno, $date);
        } catch (Exception $e) {
            $exception = $e->getMessage();
        }

        // assert
        $this->assertEquals($expected, $exception);
    }
}
