<?php
/**
 * picking items entity
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/03/06
 * @since 1.0.0 spark: config
 * @since 1.0.1 spark: file name
 */
namespace App\Models\ProductWarehouse;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PickingItems
 *
 * @package App\Models\ProductWarehouse
 */
class PickingItems extends Model
{
    //
    protected $connection = 'oracle';
    protected $table = 'JDV_F5942520';
    public $timestamps = false;
}
