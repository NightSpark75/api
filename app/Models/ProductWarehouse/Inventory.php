<?php
/**
 * inventory list entity
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/01/22
 * @since 1.0.0 spark: config
 */
namespace App\Models\ProductWarehouse;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Inventory
 *
 * @package App\Models\ProductWarehouse
 */
class Inventory extends Model
{
    //
    protected $connection = 'oracle';
    protected $table = 'MPM_INVENTORY';
    public $timestamps = false;
}
