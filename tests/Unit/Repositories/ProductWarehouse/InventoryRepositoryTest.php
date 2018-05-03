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

    public function test_checkCurrent()
    {
        // arrange
        $user = str_random(5);
        $date = '20180426';
        $cyno = DB::selectOne("
            select pjcyno
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a 
                where to_char(to_date(substr(a.pjcsdj,2,5),'YYDDD'),'YYYYMMDD') = $date
                group by pjcyno
        ")->pjcyno;       
        $expected = 1;

        // act
        DB::insert("
            insert into mpm_inventory_m
                values ('$cyno', '$user', sysdate, null, 'Y', sysdate)
        ");
        $actual = $this->target->getCurrent($user);

        // assert
        $this->assertEquals($expected, count($actual));
    }

    public function test_checkStartInventory_true()
    {
        // arrange
        $user = str_random(5);
        $date = '20180426';
        $cyno = DB::selectOne("
            select pjcyno
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a 
                where to_char(to_date(substr(a.pjcsdj,2,5),'YYDDD'),'YYYYMMDD') = $date
                group by pjcyno
        ")->pjcyno;       

        // act
        DB::delete("
            delete mpm_inventory_m where cyno = '$cyno'
        ");
        $actual = $this->target->checkStartInventory($cyno);

        // assert
        $this->assertTrue($actual);
    }

    public function test_checkStartInventory_false()
    {
        // arrange
        $user = str_random(5);
        $date = '20180426';
        $cyno = DB::selectOne("
            select pjcyno
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a 
                where to_char(to_date(substr(a.pjcsdj,2,5),'YYDDD'),'YYYYMMDD') = $date
                group by pjcyno
        ")->pjcyno;       

        // act
        DB::insert("
            insert into mpm_inventory_m
                values ('$cyno', '$user', sysdate, null, 'Y', sysdate)
        ");
        $actual = $this->target->checkStartInventory($cyno);

        // assert
        $this->assertFalse($actual);
    }

    public function test_checkInventory_true()
    {
        // arrange
        $user = str_random(5);
        $date = '20180426';
        $cyno = DB::selectOne("
            select pjcyno
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a 
                where to_char(to_date(substr(a.pjcsdj,2,5),'YYDDD'),'YYYYMMDD') = $date
                group by pjcyno
        ")->pjcyno;       

        // act
        DB::insert("
            insert into mpm_inventory_m
                values ('$cyno', '$user', sysdate, null, 'Y', sysdate)
        ");
        $actual = $this->target->checkInventory($cyno, $user);

        // assert
        $this->assertTrue($actual);
    }

    public function test_checkInventory_false()
    {
        // arrange
        $user = str_random(5);
        $date = '20180426';
        $cyno = DB::selectOne("
            select pjcyno
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a 
                where to_char(to_date(substr(a.pjcsdj,2,5),'YYDDD'),'YYYYMMDD') = $date
                group by pjcyno
        ")->pjcyno;       

        // act
        DB::delete("
            delete mpm_inventory_m where cyno = '$cyno'
        ");
        $actual = $this->target->checkInventory($cyno, $user);

        // assert
        $this->assertFalse($actual);
    }

    public function test_startInventory()
    {
        // arrange
        $cyno = str_random(4);
        $user = str_random(5);
        $expected = $cyno;

        // act
        DB::delete("delete mpm_inventory_m");
        $actual = $this->target->startInventory($cyno, $user);
        $actual2 = DB::selectOne("select * from mpm_inventory_m")->cyno;    

        // assert
        $this->assertTrue($actual);
        $this->assertEquals($expected, $actual2);
    }

    public function test_pauseInventory()
    {
        // arrange
        $cyno = str_random(4);
        $user = str_random(5);
        $expected = $cyno;

        // act
        DB::insert("
            insert into mpm_inventory_m
                values ('$cyno', '$user', sysdate, null, 'Y', sysdate)
        ");
        $actual = $this->target->pauseInventory($cyno, $user);
        $actual2 = DB::selectOne("
            select * from mpm_inventory_m 
                where cyno = '$cyno' 
                    and duser = '$user'
                    and state = 'P'
        ")->cyno;    
        // assert
        $this->assertTrue($actual);
        $this->assertEquals($expected, $actual2);
    }

    public function test_endInventory()
    {
        // arrange
        $cyno = str_random(4);
        $user = str_random(5);
        $expected = $cyno;

        // act
        DB::insert("
            insert into mpm_inventory_m
                values ('$cyno', '$user', sysdate, null, 'Y', sysdate)
        ");
        $actual = $this->target->endInventory($cyno, $user);
        $actual2 = DB::selectOne("
            select * from mpm_inventory_m 
                where cyno = '$cyno' 
                    and duser = '$user'
                    and state = 'E'
        ")->cyno;    
        // assert
        $this->assertTrue($actual);
        $this->assertEquals($expected, $actual2);
    }

    
    public function test_getInventoryList()
    {
        // arrange
        $date = '20180426';
        $user = str_random(5);
        $inventory = DB::select("
            select pjcyno
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a 
                where to_char(to_date(substr(a.pjcsdj,2,5),'YYDDD'),'YYYYMMDD') = $date
                group by pjcyno
        ");
        $cyno = $inventory[0]->pjcyno;
        $expected = count($inventory) - 1;
        
        // act
        DB::insert("
            insert into mpm_inventory_m
                values ('$cyno', '$user', sysdate, null, 'E', sysdate)
        ");
        $actual = $this->target->getInventoryList($user, $date);

        // assert
        $this->assertEquals($expected, count($actual));
    }

    public function test_getInventoryItem()
    {
        // arrange
        $date = '20180426';
        $user = str_random(5);
        $cyno = DB::selectOne("
            select pjcyno
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a 
                where to_char(to_date(substr(a.pjcsdj,2,5),'YYDDD'),'YYYYMMDD') = $date
                group by pjcyno
        ")->pjcyno;
        $inventory = DB::select("
            select *
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a 
                where pjcyno = '$cyno'
                order by pjlocn, pjlitm, pjlotn
        ");
        $expected = $inventory[1];
        
        // act
        $locn = $inventory[0]->pjlocn;
        $litm = $inventory[0]->pjlitm;
        $lotn = $inventory[0]->pjlotn;
        $amount = rand(1, 100);
        DB::insert("
            insert into mpm_inventory_d
                values ('$cyno', trim('$locn'), trim('$litm'), trim('$lotn'), $amount, '$user', sysdate)
        ");
        $actual = $this->target->getInventoryItem($cyno);

        // assert
        $this->assertEquals(trim($expected->pjlocn), $actual->locn);
        $this->assertEquals(trim($expected->pjlitm), $actual->litm);
        $this->assertEquals(trim($expected->pjlotn), $actual->lotn);
    }

    public function test_checkFinished_true()
    {
        // arrange
        $date = '20180426';
        $user = str_random(5);
        $cyno = DB::selectOne("
            select pjcyno
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a 
                where to_char(to_date(substr(a.pjcsdj,2,5),'YYDDD'),'YYYYMMDD') = $date
                group by pjcyno
        ")->pjcyno;
        $inventory = DB::select("
            select *
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a 
                where pjcyno = '$cyno'
                order by pjlocn, pjlitm, pjlotn
        ");
        
        // act
        for ($i = 0; $i < count($inventory); $i++) {
            $locn = $inventory[$i]->pjlocn;
            $litm = $inventory[$i]->pjlitm;
            $lotn = $inventory[$i]->pjlotn;
            $amount = rand(1, 100);
            DB::insert("
                insert into mpm_inventory_d
                    values ('$cyno', trim('$locn'), trim('$litm'), trim('$lotn'), $amount, '$user', sysdate)
            ");
        }
        $actual = $this->target->checkFinished($cyno);

        // assert
        $this->assertTrue($actual);
    }

    public function test_checkFinished_false()
    {
        // arrange
        $date = '20180426';
        $user = str_random(5);
        $cyno = DB::selectOne("
            select pjcyno
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a 
                where to_char(to_date(substr(a.pjcsdj,2,5),'YYDDD'),'YYYYMMDD') = $date
                group by pjcyno
        ")->pjcyno;
        
        // act
        $actual = $this->target->checkFinished($cyno);

        // assert
        $this->assertFalse($actual);
    }

    public function test_saveInventory()
    {
        // arrange
        $date = '20180426';
        $user = str_random(5);
        $cyno = DB::selectOne("
            select pjcyno
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a 
                where to_char(to_date(substr(a.pjcsdj,2,5),'YYDDD'),'YYYYMMDD') = $date
                group by pjcyno
        ")->pjcyno;
        $inventory = DB::select("
            select *
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a 
                where pjcyno = '$cyno'
                order by pjlocn, pjlitm, pjlotn
        ");
        $locn = trim($inventory[0]->pjlocn);
        $litm = trim($inventory[0]->pjlitm);
        $lotn = trim($inventory[0]->pjlotn);
        $amount = rand(1, 100);
        $expected = 1;
        
        // act
        $actual = $this->target->saveInventory($user, $cyno, $locn, $litm, $lotn, $amount);
        $actual2 = count(DB::select("
            select *
                from mpm_inventory_d
                where cyno = '$cyno'
        "));

        // assert
        $this->assertTrue($actual);
        $this->assertEquals($expected, $actual2);
    }

    public function test_checkInventoryUser()
    {
        // arrange
        $date = '20180426';
        $user = str_random(5);
        $cyno = DB::selectOne("
            select pjcyno
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a 
                where to_char(to_date(substr(a.pjcsdj,2,5),'YYDDD'),'YYYYMMDD') = $date
                group by pjcyno
        ")->pjcyno;
        $inventory = DB::select("
            select *
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a 
                where pjcyno = '$cyno'
                order by pjlocn, pjlitm, pjlotn
        ");
        $locn = trim($inventory[0]->pjlocn);
        $litm = trim($inventory[0]->pjlitm);
        $lotn = trim($inventory[0]->pjlotn);
        $amount = rand(1, 100);
        
        // act
        DB::insert("
            insert into mpm_inventory_d
                values ('$cyno', trim('$locn'), trim('$litm'), trim('$lotn'), $amount, '$user', sysdate)
        ");
        $actual = $this->target->checkInventoryUser($user, $cyno);

        // assert
        $this->assertTrue($actual);
    }

    public function test_inventoried()
    {
        // arrange
        $date = '20180426';
        $user = str_random(5);
        $cyno = DB::selectOne("
            select pjcyno
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a 
                where to_char(to_date(substr(a.pjcsdj,2,5),'YYDDD'),'YYYYMMDD') = $date
                group by pjcyno
        ")->pjcyno;
        $inventory = DB::select("
            select *
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a 
                where pjcyno = '$cyno'
                order by pjlocn, pjlitm, pjlotn
        ");
        $locn = trim($inventory[0]->pjlocn);
        $litm = trim($inventory[0]->pjlitm);
        $lotn = trim($inventory[0]->pjlotn);
        $amount = rand(1, 100);
        $expected = 1;
        
        // act
        DB::insert("
            insert into mpm_inventory_d
                values ('$cyno', trim('$locn'), trim('$litm'), trim('$lotn'), $amount, '$user', sysdate)
        ");
        $actual = $this->target->inventoried($cyno);

        // assert
        $this->assertEquals($expected, count($actual));
    }

    public function test_exportData()
    {
        // arrange
        $header = [['盤點數量', '儲位', '料號', '批號', '盤點人員', '時間']];
        $date = '20180426';
        $user = str_random(5);
        $cyno = DB::selectOne("
            select pjcyno
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a 
                where to_char(to_date(substr(a.pjcsdj,2,5),'YYDDD'),'YYYYMMDD') = $date
                group by pjcyno
        ")->pjcyno;
        $inventory = DB::select("
            select *
                from proddta.jt4141A@JDBPRD.STANDARD.COM.TW a 
                where pjcyno = '$cyno'
                order by pjlocn, pjlitm, pjlotn
        ");
        $locn = trim($inventory[0]->pjlocn);
        $litm = trim($inventory[0]->pjlitm);
        $lotn = trim($inventory[0]->pjlotn);
        $amount = rand(1, 100);
        $expected = 2;
        
        // act
        DB::insert("
            insert into mpm_inventory_d
                values ('$cyno', trim('$locn'), trim('$litm'), trim('$lotn'), $amount, '$user', sysdate)
        ");

        $expected = 2;

        // act
        $actual = $this->target->exportData($cyno);

        // assert
        $this->assertEquals($expected, count($actual));
    }
}
