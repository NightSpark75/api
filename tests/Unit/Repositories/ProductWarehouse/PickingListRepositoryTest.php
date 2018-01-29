<?php
/**
 * PickingListRepository unit test
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/01/29
 * @since 1.0.0 spark: complete 2 test
 * 
 */
namespace Tests\Unit\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Exception;
use App\Models\ProductWarehouse\PickingList;
use APp\Repositories\ProductWarehouse\PickingListRepository;

/**
 * Class PickingListRepositoryTest
 *
 * @package Tests\Unit\Repositories
 */
class PickingListRepositoryTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;
    
    /**
     * @var PickingListRepository
     */
    private $target;

    /**
     * setUp()
     */
    public function setUp()
    {
        parent::setUp();
        $this->target = $this->app->make(PickingListRepository::class);
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
     * test getPickingList()
     */
    public function test_getPickingList()
    {
        // arrange
        $first = PickingList::where('staddj', '2015-12-09 00:00:00')->where('stky2', null)->first();
        $date = $first->staddj;
        $expected = PickingList::where('staddj', $date)->where('stky2', null)->get();
        
        // act
        $actual = $this->target->getPickingList($date);

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test getPicking()
     */
    public function test_getPicking()
    {
        // arrange
        $first = PickingList::first();
        $stop = $first->ststop;
        $staddj = $first->staddj;
        $expected = PickingList::where('ststop', $stop)
            ->where('staddj', $staddj)
            ->first();
        
        // act
        $actual = $this->target->getPicking($stop, $staddj);

        // assert
        $this->assertEquals($expected, $actual);
    }
}
