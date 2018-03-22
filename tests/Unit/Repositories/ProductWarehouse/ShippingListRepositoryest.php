<?php
/**
 * ShippingListRepository unit test
 *
 * @version 1.0.2
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/03/20
 * @since 1.0.0 spark: complete 4 test
 * @since 1.0.2 spark: completed unit test and optimized code
 * 
 */
namespace Tests\Unit\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Exception;
use App\Models\ProductWarehouse\ShippingList;
use App\Repositories\ProductWarehouse\ShippingListRepository;

/**
 * Class ShippingListRepositoryTest
 *
 * @package Tests\Unit\Repositories
 */
class ShippingListRepositoryTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;
    
    /**
     * @var ShippingListRepository
     */
    private $target;

    /**
     * setUp()
     */
    public function setUp()
    {
        parent::setUp();
        $this->target = $this->app->make(ShippingListRepository::class);
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
     * test getShippingList()
     */
    public function test_getShippingList()
    {
        // arrange
        $first = ShippingList::first();
        $date = $first->staddj;
        $expected = 
            ShippingList::where('staddj', $date)
                ->where('stky6', null)
                ->select('sticu', 'ststop', 'staddj', 'stky2')
                ->orderBy('ststop')
                ->get();
        
        // act
        $actual = $this->target->getShippingList($date);

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test getShipping()
     */
    public function test_getShipping()
    {
        // arrange
        $first = ShippingList::first();
        $stop = $first->ststop;
        $staddj = $first->staddj;
        $expected = 
            ShippingList::where('ststop', $stop)
                ->where('staddj', $staddj)
                ->first();
        
        // act
        $actual = $this->target->getShipping($stop, $staddj);

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test startShipping()
     */
    public function test_startShipping()
    {
        // arrange
        $stop = 'A1';
        $date = '13-MAR-18';
        $user = '106013';

        // act
        $actual = $this->target->startShipping($stop, $date, $user);

        // assert
        $this->assertTrue($actual);
    }

    /**
     * test endShipping()
     */
    public function test_endShipping()
    {
        // arrange
        $stop = 'A1';
        $date = '13-MAR-18';
        $user = '106013';

        // act
        $actual = $this->target->endShipping($stop, $date, $user);

        // assert
        $this->assertTrue($actual);
    }
}
