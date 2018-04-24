<?php
/**
 * InventoryRepository unit test
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/04/24
 * @since 1.0.0 spark: complete
 * 
 */
namespace Tests\Unit\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use DB;
use Exception;
use App\Repositories\ProductWarehouse\InventoryRepository;

/**
 * Class ShippingListRepositoryTest
 *
 * @package Tests\Unit\Repositories
 */
class InventoryRepositoryTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;
    
    /**
     * @var InventoryRepository
     */
    private $target;

    /**
     * setUp()
     */
    public function setUp()
    {
        parent::setUp();
        $this->target = $this->app->make(InventoryRepository::class);
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
    
    public function test_getInventoryList()
    {
        // arrange
        $date = '20180416';
        $expected = DB::select("
            select unique pjcyno cyno from proddta.jt4141A@JDBPRD.STANDARD.COM.TW
                where to_char(to_date(substr(pjcsdj,2,5),'YYDDD'),'YYYYMMDD') = '$date'
        ");
        
        // act
        $actual = $this->target->getInventoryList($date);

        // assert
        $this->assertEquals($expected, $actual);
    }

    public function test_getInventoryList_data_is_finished()
    {
        // arrange
        $date = '20180416';
        DB::insert("
            insert into mpm_inventory
                select trim(pjcyno), trim(pjlocn), trim(pjlitm), trim(pjlotn), 0, '123456', sysdate
                    from proddta.jt4141A@JDBPRD.STANDARD.COM.TW
                    where to_char(to_date(substr(pjcsdj,2,5),'YYDDD'),'YYYYMMDD') = '$date'
        ");
        $expected = [];
        
        // act
        $actual = $this->target->getInventoryList($date);

        // assert
        $this->assertEquals($expected, $actual);
    }

    public function test_getInventoryItem()
    {
        // arrange
        $cyno = '10000740';
        DB::insert("
            insert into mpm_inventory
            select trim(pjcyno), trim(pjlocn), trim(pjlitm), trim(pjlotn), 0, '123456', sysdate
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW
                where pjcyno = '$cyno' and substr(pjlocn, 2, 1) = '1'
        ");
        $expected = DB::selectOne("
            select  a.pjcyno cyno, --盤點號碼
                    a.pjcsdj csdj, --盤點日期
                    trim(a.pjlocn) locn, --儲位
                    trim(a.pjlitm) litm, --料號
                    trim(a.pjlotn) lotn, --批號
                    a.pjtqoh tqoh, --庫存量
                    a.pjuom1 uom1,  --庫存單位
                    a.pjtqoh amount
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a
                where pjcyno = '$cyno' and substr(pjlocn, 2, 1) <> '1'
                order by pjlocn, pjlitm, pjlotn
        ");
        
        // act
        $actual = $this->target->getInventoryItem($cyno);

        // assert
        $this->assertEquals($expected, $actual);
    }

    public function test_checkFinished_true()
    {
        // arrange
        $cyno = '10000740';
        DB::insert("
            insert into mpm_inventory
                select trim(pjcyno), trim(pjlocn), trim(pjlitm), trim(pjlotn), 0, '123456', sysdate
                    from proddta.jt4141A@JDBPRD.STANDARD.COM.TW
                    where pjcyno = '$cyno'
        ");
        $expected = true;

        // act
        $actual = $this->target->checkFinished($cyno);

        // assert
        $this->assertEquals($expected, $actual);
    }

    public function test_checkFinished_false()
    {
        // arrange
        $cyno = '10000740';
        $expected = false;

        // act
        $actual = $this->target->checkFinished($cyno);

        // assert
        $this->assertEquals($expected, $actual);
    }

    public function test_saveInventory()
    {
        // arrange
        $id = '123456';
        $cyno = '10000740';
        $data = DB::selectOne("
            select trim(pjlocn) locn, trim(pjlitm) litm, trim(pjlotn) lotn
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW
                where pjcyno = '$cyno'
        ");
        $locn = $data->locn;
        $litm = $data->litm;
        $lotn = $data->lotn;
        $amount = 0;
        $expected = true;

        // act
        $actual = $this->target->saveInventory($id, $cyno, $locn, $litm, $lotn, $amount);
        $count = (int) DB::selectOne("
            select count(*) n
                from mpm_inventory
                where cyno = '$cyno'
        ")->n;

        // assert
        $this->assertEquals($expected, $actual);
        $this->assertEquals($count, 1);
    }

    public function test_checkInventoryUser_true()
    {
        // arrange
        $id = '123456';
        $cyno = '10000740';
        DB::insert("
            insert into mpm_inventory
                select trim(pjcyno), trim(pjlocn), trim(pjlitm), trim(pjlotn), 0, '$id', sysdate
                    from proddta.jt4141A@JDBPRD.STANDARD.COM.TW
                    where pjcyno = '$cyno'
        ");
        $expected = true;

        // act
        $actual = $this->target->checkInventoryUser($id, $cyno);

        // assert
        $this->assertEquals($expected, $actual);
    }

    public function test_checkInventoryUser_false()
    {
        // arrange
        $id = '654321';
        $cyno = '10000740';
        DB::insert("
            insert into mpm_inventory
                select trim(pjcyno), trim(pjlocn), trim(pjlitm), trim(pjlotn), 0, '123456', sysdate
                    from proddta.jt4141A@JDBPRD.STANDARD.COM.TW
                    where pjcyno = '$cyno'
        ");
        $expected = false;

        // act
        $actual = $this->target->checkInventoryUser($id, $cyno);

        // assert
        $this->assertEquals($expected, $actual);
    }

    public function test_inventoried()
    {
        // arrange
        $cyno = '10000740';
        DB::insert("
            insert into mpm_inventory
                select trim(pjcyno), trim(pjlocn), trim(pjlitm), trim(pjlotn), 0, '123456', sysdate
                    from proddta.jt4141A@JDBPRD.STANDARD.COM.TW
                    where pjcyno = '$cyno' and rownum = 1
        ");
        $expected = 1;

        // act
        $actual = $this->target->inventoried($cyno);

        // assert
        $this->assertEquals($expected, count($actual));
    }

    public function test_exportData()
    {
        // arrange
        $cyno = '10000740';
        DB::insert("
            insert into mpm_inventory
                select trim(pjcyno), trim(pjlocn), trim(pjlitm), trim(pjlotn), 0, '123456', sysdate
                    from proddta.jt4141A@JDBPRD.STANDARD.COM.TW
                    where pjcyno = '$cyno' and rownum = 1
        ");
        $expected = 2;
        // act
        $actual = $this->target->exportData($cyno);

        // assert
        $this->assertEquals($expected, count($actual));
    }
}
