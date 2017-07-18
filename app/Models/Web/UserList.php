<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class UserList extends Model
{
    //
    protected $connection = 'oracle';
    protected $table = "sma_user_m";
    public $timestamps = false;
}
