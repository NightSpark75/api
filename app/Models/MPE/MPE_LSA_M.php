<?php

namespace App\Models\MPZ;

use Illuminate\Database\Eloquent\Model;

class MPZ_CATCHLOG extends Model
{
    //
    protected $connection = 'oracle';
    protected $table = "MPE_LSA_M";
}
