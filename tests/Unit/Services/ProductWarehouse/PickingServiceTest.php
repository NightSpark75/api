<?php
/**
 * PickingService unit test
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
use App\Services\ProductWarehouse\PickingService;
use App\Repositories\ProductWarehouse\PickingListRepository;
use App\Repositories\ProductWarehouse\PickingItemsRepository;
use App\Models\ProductWarehouse\PickingList;
use App\Models\ProductWarehouse\PickingItems;

/**
 * Class PickingServiceTest
 *
 * @package Tests\Unit\Services\ProductWarehouse
 */
class PickingServiceTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;
    
    /**
     * @var PickingListRepository
     */
    private $mock;
    
    /**
     * @var PickingService
     */
    private $target;

    /**
     * setUp()
     */
    public function setUp()
    {
        parent::setUp();
        $this->mock = $this->initMock(PickingListRepository::class);
        $this->target = $this->app->make(PickingService::class);
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
     * test getTodayPickingList()
     */
    public function test_getTodayPickingList()
    {
        // arrange
        $expected = PickingList::where('stky6', null)->first();
        $date = $expected->staddj;

        // act
        $this->mock->shouldReceive('getPickingList')
            ->once()
            ->with($date)
            ->andReturn($expected);
        $actual = $this->target->getTodayPickingList($date);

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test startPicking() 
     * call procedure proc_upd_f594921 and check the data is updated
     */
    public function test_startPicking()
    {
        // arrange
        $data = PickingList::where('stky6', null)->first();   
        $stop = $data->ststop;
        $empno = 'test user';
        $datetime = $data->staddj;
        $staddj = date_format(date_create($datetime), 'Y/m/d');

        // act
        $this->mock->shouldReceive('getPicking')
            ->once()
            ->with($stop, $datetime)
            ->andReturn($data);
        
        $this->mock->shouldReceive('startPicking')
            ->once()
            ->with($stop, $staddj, $empno);
        
        $actual = $this->target->startPicking($stop, $empno, $datetime);

        // assert
        $this->assertTrue($actual);
    }

    /**
     * test startPicking(), throw no data found Exception 
     */
    public function test_startPicking_exception()
    {
        // arrange
        $stop = 'test';
        $empno = '50001';
        $datetime = '1911-01-01 00:00:00';
        $expected = "ststop='$stop' and staddj='$datetime', data not found!";

        // act
        $this->mock->shouldReceive('getPicking')
            ->once()
            ->with($stop, $datetime)
            ->andReturn(null);

        try {
            $actual = $this->target->startPicking($stop, $empno, $datetime);
        } catch (Exception $e) {
            $actual = $e->getMessage();
        }

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test endPicking() 
     * call procedure proc_upd_f594921 and check the data is updated
     */
    public function test_endPicking()
    {
        // arrange
        $data = PickingList::where('stky6', null)->first();   
        $stop = $data->ststop;
        $empno = 'test user';
        $datetime = $data->staddj;
        $staddj = date_format(date_create($datetime), 'Y/m/d');

        // act
        $this->mock->shouldReceive('getPicking')
            ->once()
            ->with($stop, $datetime)
            ->andReturn($data);
        
        $this->mock->shouldReceive('endPicking')
            ->once()
            ->with($stop, $staddj, $empno);
        
        $actual = $this->target->endPicking($stop, $empno, $datetime);

        // assert
        $this->assertTrue($actual);
    }


    /**
     * tes endPicking(), throw no data found Exception 
     */
    public function test_endPicking_exception()
    {
        // arrange
        $stop = 'test';
        $empno = '50001';
        $datetime = '1911-01-01 00:00:00';
        $expected = "ststop='$stop' and staddj='$datetime', data not found!";

        // act
        $this->mock->shouldReceive('getPicking')
            ->once()
            ->with($stop, $datetime)
            ->andReturn(null);

        try {
            $actual = $this->target->endPicking($stop, $empno, $datetime);
        } catch (Exception $e) {
            $actual = $e->getMessage();
        }

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test getPickingItems() 
     */
    public function test_getPickingItems()
    {
        // arrange
        $this->mock = $this->initMock(PickingItemsRepository::class);
        $this->target = $this->app->make(PickingService::class);
        $items = PickingItems::first();
        $stop = $items->psstop;
        $date = $items->psaddj;

        $expected = 
            PickingItems::where('psstop', $stop)
                ->where('psaddj', $date)
                ->select('psicu', 'psaddj', 'psstop', 'pslocn', 'psrmk', 'pslitm', 'pslotn', 'pssoqs', 'pspqoh', 'psuom')
                ->orderBy('pslocn')
                ->orderBy('psrmk')
                ->orderBy('pslitm')
                ->get();

        // act
        $this->mock->shouldReceive('getPickingItems')
            ->times(1)
            ->with(trim($stop), $date)
            ->andReturn($expected);
        $actual = $this->target->getPickingItems(trim($stop), $date);

        // assert
        $this->assertEquals($expected, $actual);
    }
}
