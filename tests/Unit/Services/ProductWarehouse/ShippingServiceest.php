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
     * test getTodayShippingList()
     */
    public function test_getTodayShippingList()
    {
        // arrange
        $expected = ShippingList::where('stky6', null)->first();
        $date = $expected->staddj;

        // act
        $this->mock->shouldReceive('getShippingList')
            ->once()
            ->with($date)
            ->andReturn($expected);
        $actual = $this->target->getTodayShippingList($date);

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test startShipping() 
     * call procedure proc_upd_f594921 and check the data is updated
     */
    public function test_startShipping()
    {
        // arrange
        $data = ShippingList::where('stky6', null)->first();   
        $stop = $data->ststop;
        $empno = 'test user';
        $datetime = $data->staddj;
        $staddj = date_format(date_create($datetime), 'Y/m/d');

        // act
        $this->mock->shouldReceive('getShipping')
            ->once()
            ->with($stop, $datetime)
            ->andReturn($data);
        
        $this->mock->shouldReceive('startShipping')
            ->once()
            ->with($stop, $staddj, $empno);
        
        $actual = $this->target->startShipping($stop, $empno, $datetime);

        // assert
        $this->assertTrue($actual);
    }

    /**
     * test startShipping(), throw no data found Exception 
     */
    public function test_startShipping_exception()
    {
        // arrange
        $stop = 'test';
        $empno = '50001';
        $datetime = '1911-01-01 00:00:00';
        $expected = "ststop='$stop' and staddj='$datetime', data not found!";

        // act
        $this->mock->shouldReceive('getShipping')
            ->once()
            ->with($stop, $datetime)
            ->andReturn(null);

        try {
            $actual = $this->target->startShipping($stop, $empno, $datetime);
        } catch (Exception $e) {
            $actual = $e->getMessage();
        }

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test endShipping() 
     * call procedure proc_upd_f594921 and check the data is updated
     */
    public function test_endShipping()
    {
        // arrange
        $data = ShippingList::where('stky6', null)->first();   
        $stop = $data->ststop;
        $empno = 'test user';
        $datetime = $data->staddj;
        $staddj = date_format(date_create($datetime), 'Y/m/d');

        // act
        $this->mock->shouldReceive('getShipping')
            ->once()
            ->with($stop, $datetime)
            ->andReturn($data);
        
        $this->mock->shouldReceive('endShipping')
            ->once()
            ->with($stop, $staddj, $empno);
        
        $actual = $this->target->endShipping($stop, $empno, $datetime);

        // assert
        $this->assertTrue($actual);
    }


    /**
     * tes endShipping(), throw no data found Exception 
     */
    public function test_endShipping_exception()
    {
        // arrange
        $stop = 'test';
        $empno = '50001';
        $datetime = '1911-01-01 00:00:00';
        $expected = "ststop='$stop' and staddj='$datetime', data not found!";

        // act
        $this->mock->shouldReceive('getShipping')
            ->once()
            ->with($stop, $datetime)
            ->andReturn(null);

        try {
            $actual = $this->target->endShipping($stop, $empno, $datetime);
        } catch (Exception $e) {
            $actual = $e->getMessage();
        }

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test getShippingItems() 
     */
    public function test_getShippingItems()
    {
        // arrange
        $this->mock = $this->initMock(ShippingItemsRepository::class);
        $this->target = $this->app->make(ShippingService::class);
        $items = ShippingItems::first();
        $stop = $items->psstop;
        $date = $items->psaddj;

        $expected = 
            ShippingItems::where('psstop', $stop)
                ->where('psaddj', $date)
                ->select('psicu', 'psaddj', 'psstop', 'pslocn', 'psrmk', 'pslitm', 'pslotn', 'pssoqs', 'pspqoh', 'psuom')
                ->orderBy('pslocn')
                ->orderBy('psrmk')
                ->orderBy('pslitm')
                ->get();

        // act
        $this->mock->shouldReceive('getShippingItems')
            ->times(1)
            ->with(trim($stop), $date)
            ->andReturn($expected);
        $actual = $this->target->getShippingItems(trim($stop), $date);

        // assert
        $this->assertEquals($expected, $actual);
    }
}
