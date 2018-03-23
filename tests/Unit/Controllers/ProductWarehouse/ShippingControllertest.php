<?php
/**
 * ShippingController unit test
 *
 * @version 1.0.2
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/03/20
 * @since 1.0.0 spark: complete 8 test
 * @since 1.0.2 spark: completed unit test and optimized code
 * 
 */
namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Controllers\ProductWarehouse\ShippingController;
use App\Services\ProductWarehouse\ShippingService;
use Exception;
use App\Models\Web\User;

/**
 * Class ShippingControllerTest
 *
 * @package Tests\Unit\Controllers
 */
class ShippingControllersTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;
    
    /**
     * @var ShippingService
     */
    private $mock;
    
    /**
     * @var ShippingController
     */
    private $target;

    /**
     * setUp()
     */
    public function setUp()
    {
        parent::setUp();
        $this->mock = $this->initMock(ShippingService::class);
        $this->target = $this->app->make(ShippingController::class);
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
     * test getShippingInfo()
     */
    public function test_getShippingInfo()
    {
        // arrange
        $expected = [];
        $date = null;
        $spno = '123456';

        // act
        $this->mock->shouldReceive('getShippingInfo')
            ->once()
            ->with($spno, $date)
            ->andReturn($expected);
        $actual = $this->target->getShippingInfo($spno, $date);

        // assert
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
    }
}
