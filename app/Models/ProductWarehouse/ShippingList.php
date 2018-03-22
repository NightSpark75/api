<?php
/**
 * picking list entity
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/01/22
 * @since 1.0.0 spark: config
 */
namespace App\Models\ProductWarehouse;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ShippingList
 *
 * @package App\Models\ProductWarehouse
 */
class ShippingList extends Model
{
    //
    protected $connection = 'oracle';
    protected $table = 'JDV_F594901';
    public $timestamps = false;
}
