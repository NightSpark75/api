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

use DB;
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
        $date = '20180503';
        $user = str_random(5);
        $stop = DB::selectOne("
            select psstop
                from jdv_f5942520 
                where psaddj = to_date('$date', 'YYYYMMDD')
                group by psstop
        ")->psstop;
        $picking = DB::select("
            select *
                from jdv_f5942520
                where psstop = '$stop'
                    and psaddj = to_date('$date', 'YYYYMMDD')
        ");

        $rmk = trim($picking[0]->psrmk);
        $litm = trim($picking[0]->pslitm);
        $lotn = trim($picking[0]->pslotn);
        $expected = count($picking) - 1;
        
        // act
        DB::insert("
            insert into mpm_picking_d
                values (trim('$stop'), $date, '$rmk', '$litm', '$lotn', '$user', sysdate)
        ");
        $actual = $this->target->getPickingItems($stop, $date);

        // assert
        $this->assertEquals($expected, count($actual));
    }

    /**
     * test pickup
     */
    public function test_pickup()
    {
        // arrange
        $date = '20180503';
        $user = str_random(5);
        $stop = DB::selectOne("
            select psstop
                from jdv_f5942520 
                where psaddj = to_date('$date', 'YYYYMMDD')
                group by psstop
        ")->psstop;
        $picking = DB::select("
            select *
                from jdv_f5942520
                where psstop = '$stop'
                    and psaddj = to_date('$date', 'YYYYMMDD')
        ");

        $rmk = trim($picking[0]->psrmk);
        $litm = trim($picking[0]->pslitm);
        $lotn = trim($picking[0]->pslotn);
        
        // act
        $actual = $this->target->pickup($stop, $date, $rmk, $litm, $lotn, $user);
        
        // assert
        $this->assertTrue($actual);
    }
}
