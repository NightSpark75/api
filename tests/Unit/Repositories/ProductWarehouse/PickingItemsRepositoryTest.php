<?php
/**
 * PickingListRepository unit test
 *
 * @version 1.0.2
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/03/20
 * @since 1.0.0 spark: complete 1 test
 * @since 1.0.2 spark: completed unit test and optimized code
 * 
 */
namespace Tests\Unit\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Exception;
use App\Models\ProductWarehouse\PickingItems;
use App\Repositories\ProductWarehouse\PickingItemsRepository;

/**
 * Class PickingRepositoryTest
 *
 * @package Tests\Unit\Repositories
 */
class PickingItemsRepositoryTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;
    
    /**
     * @var PickingItemsRepository
     */
    private $target;

    /**
     * setUp()
     */
    public function setUp()
    {
        parent::setUp();
        $this->target = $this->app->make(PickingItemsRepository::class);
    }

    /**
     * test getPickingItems
     */
    public function test_getPickingItems()
    {
        // arrange
        $first = PickingItems::first();
        $stop = $first->psstop;
        $date = $first->psaddj;
        $expected = 
            PickingItems::where('psaddj', $date)
                ->where('psstop', $stop)
                ->select('psicu', 'psaddj', 'psstop', 'pslocn', 'psrmk', 'pslitm', 'pslotn', 'pssoqs', 'pspqoh', 'psuom')
                ->orderBy('psseq')
                ->orderBy('pslocn')
                ->orderBy('psrmk')
                ->orderBy('pslitm')
                ->get();
        // act
        $actual = $this->target->getPickingItems($stop, $date);

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test pickup
     */
    public function test_pickup()
    {
        // arrange
        $stop = '';
        $date = '';
        $user = '';
        $rmk = '';
        $litm = '';
        $lotn = '';
        
        // act
        $actual = $this->target->pickup($stop, $date, $rmk, $litm, $lotn, $user);
        
        // assert
        $this->assertTrue($actual);
    }
}
