<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class ApiLoginLog extends Model
{
    //
    protected $connection = 'oracle';
    protected $table = "api_login_log";
    protected $primaryKey = 'log_id';
    public $timestamps = false;
}
