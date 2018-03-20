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
        $first = PickingList::first();
        $date = $first->staddj;
        $expected = PickingList::where('staddj', $date)
                        ->where('stky6', null)
                        ->select('sticu', 'ststop', 'staddj', 'stky2')
                        ->orderBy('ststop')
                        ->get();
        
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

    /**
     * test startPicking()
     */
    public function test_startPicking()
    {
        // arrange
        $stop = 'A1';
        $date = '13-MAR-18';
        $user = '106013';

        // act
        $actual = $this->target->startPicking($stop, $date, $user);

        // assert
        $this->assertTrue($actual);
    }

    /**
     * test endPicking()
     */
    public function test_endPicking()
    {
        // arrange
        $stop = 'A1';
        $date = '13-MAR-18';
        $user = '106013';

        // act
        $actual = $this->target->endPicking($stop, $date, $user);

        // assert
        $this->assertTrue($actual);
    }
}
