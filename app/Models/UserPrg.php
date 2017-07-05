<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPrg extends Model
{
    //
    protected $connection = 'oracle';
    protected $table = "v_user_prg";
}
