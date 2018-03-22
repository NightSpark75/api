<?php
/**
 * picking items entity
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/03/06
 * @since 1.0.0 spark: config
 */
namespace App\Models\ProductWarehouse;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ShippingItems
 *
 * @package App\Models\ProductWarehouse
 */
class ShippingItems extends Model
{
    //
    protected $connection = 'oracle';
    protected $table = 'JDV_F594211';
    public $timestamps = false;
}
