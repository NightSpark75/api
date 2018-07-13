<?php
/*alice:
MPB 生產報表
MPE QA QC
MPZ 倉管表單
*/
namespace App\Models\MPE;

use Illuminate\Database\Eloquent\Model;

class MPE_LSA_M extends Model
{
    //
    protected $connection = 'oracle';
    protected $table = "MPE_LSA_M";
}
