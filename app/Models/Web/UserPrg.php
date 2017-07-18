<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class UserPrg extends Model
{
    //
    protected $connection = 'oracle';
    protected $table = "v_user_prg";
}
