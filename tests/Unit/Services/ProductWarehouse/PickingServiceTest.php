<?php
/**
 * PickingService unit test
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/01/29
 * @since 1.0.0 spark: complete six test
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
use App\Models\ProductWarehouse\PickingList;

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
        $today = PickingList::first()->staddj;
        $expected = PickingList::where('staddj', $today)->where('stky3', null)->get();

        // act
        $this->mock->shouldReceive('getPickingList')
            ->once()
            ->with($today)
            ->andReturn($expected);
        $actual = $this->target->getTodayPickingList($today);

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
        $data = PickingList::first();
        $stop = $data->ststop;
        $empno = 'test user';
        $datetime = $data->staddj;
        $ky3 = '99:99:99';

        // act
        $this->mock->shouldReceive('getPicking')
            ->once()
            ->with($stop, $datetime)
            ->andReturn($data);
        $actual = $this->target->startPicking($stop, $empno, $datetime, $ky3);
        $updated = PickingList::first();

        // assert
        $this->assertTrue($actual);
        $this->assertEquals($empno, trim($updated->stky2));
        $this->assertEquals($ky3, trim($updated->stky3));
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
        $data = PickingList::first();
        $stop = $data->ststop;
        $empno = $data->empno;
        $datetime = $data->staddj;
        $ky6 = 'T';
        $ky7 = '88:88:88';

        // act
        $this->mock->shouldReceive('getPicking')
            ->once()
            ->with($stop, $datetime)
            ->andReturn($data);
        $actual = $this->target->endPicking($stop, $empno, $datetime, $ky6, $ky7);
        $updated = PickingList::first();
        
        // assert
        $this->assertTrue($actual);
        $this->assertEquals($ky6, trim($updated->stky6));
        $this->assertEquals($ky7, trim($updated->stky7));
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
        $stop = '';
        $expected = true;

        // act
        $actual = $this->target->getPickingItems($stop);

        // assert
        $this->assertTrue($actual);
    }
}
