<?php
/**
 * Shipping entity
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/04/25
 * @since 1.0.0 spark: config
 */
namespace App\Models\ProductWarehouse;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Shipping
 *
 * @package App\Models\ProductWarehouse
 */
class Shipping extends Model
{
    //
    protected $connection = 'oracle';
    protected $table = 'MPM_SHIPPING';
    public $timestamps = false;
}
