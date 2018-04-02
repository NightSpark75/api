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
        $spno = $first->tmy59spno;
        $date = $first->staddj;
        $expected = 
            ShippingList::where('tmy59spno', $spno)
                ->where('tmtrdj', $date)
                ->select('tmtrdj', 'tmaddj', 'tmy59spno', 'tmcars', 'cars_na', 'tman8', 'tmalph', 'tm1in1', 'dltm_na', 'tmalph1')
                ->first();
        // act
        $actual = $this->target->getShippingInfo($spno, $date);

        // assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test savePieces
     */
    public function test_savePieces()
    {
        //arrange
        $spno = 'test';
        $date = '';
        $user = '';
        $pieces = '';

        //act
        $actual = $this->target->savePieces($spno, $date, $user, $pieces);

        //assert
        $this->assertTrue($actual);
    }

    /**
     * test checkShippingInfo success
     */
    public function test_checkShippingInfo_success()
    {
        //arrange
        $data = ShippingList::first();
        $spno = $data->tmy59spno;
        $tmtrdj = $data->tmtrdj;
        $expected = 1;

        //act
        $actual = $this->target->checkShippingInfo($spno, $tmtrdj);

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * test checkShippingInfo fail
     */
    public function test_checkShippingInfo_fail()
    {
        //arrange
        $spno = '';
        $tmtrdj = '';
        $expected = 0;

        //act
        $actual = $this->target->checkShippingInfo($spno, $tmtrdj);

        //assert
        $this->assertEquals($expected, $actual);
    }
}
