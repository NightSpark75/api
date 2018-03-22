<?php
/**
 * ShippingListRepository unit test
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
use App\Models\ProductWarehouse\ShippingItems;
use App\Repositories\ProductWarehouse\ShippingItemsRepository;

/**
 * Class ShippingRepositoryTest
 *
 * @package Tests\Unit\Repositories
 */
class ShippingItemsRepositoryTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;
    
    /**
     * @var ShippingItemsRepository
     */
    private $target;

    /**
     * setUp()
     */
    public function setUp()
    {
        parent::setUp();
        $this->target = $this->app->make(ShippingItemsRepository::class);
    }

    /**
     * test getShippingItems
     */
    public function test_getShippingItems()
    {
        // arrange
        $first = ShippingItems::first();
        $stop = $first->psstop;
        $date = $first->psaddj;
        $expected = 
            ShippingItems::where('psaddj', $date)
                ->where('psstop', $stop)
                ->select('psicu', 'psaddj', 'psstop', 'pslocn', 'psrmk', 'pslitm', 'pslotn', 'pssoqs', 'pspqoh', 'psuom')
                ->orderBy('pslocn')
                ->orderBy('psrmk')
                ->orderBy('pslitm')
                ->get();
        // act
        $actual = $this->target->getShippingItems($stop, $date);

        // assert
        $this->assertEquals($expected, $actual);
    }
}
