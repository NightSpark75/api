<?php
/**
 * mpm_picking_d entity
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/04/25
 * @since 1.0.0 spark: config
 */
namespace App\Models\ProductWarehouse;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PickingDetail
 *
 * @package App\Models\ProductWarehouse
 */
class PickingDetail extends Model
{
    //
    protected $connection = 'oracle';
    protected $table = 'MPM_PICKING_D';
    public $timestamps = false;
}
