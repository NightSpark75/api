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

    public function test_getPickingItems()
    {
        // arrange
        $stop = '';
        $date = '';
        $expected = true;

        // act
        $actual = $this->target->getPickingItems($stop, $date);

        // assert
        $this->assertTrue($actual);
    }

    public function test_getPicking()
    {
        // arrange
        $stop = '';
        $date = '';
        $expected = true;

        // act
        $actual = $this->target->getPicking($stop, $date);

        // assert
        $this->assertTrue($actual);
    }
}
