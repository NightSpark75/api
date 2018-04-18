<?php
/**
 * api mail recipient entity
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/01/22
 * @since 1.0.0 spark: config
 */
namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

/**
 * Class mail recipient
 *
 * @package App\Models\ProductWarehouse
 */
class Recipient extends Model
{
    //
    protected $connection = 'oracle';
    protected $table = 'api_mail_recipient';
    public $timestamps = false;
}
