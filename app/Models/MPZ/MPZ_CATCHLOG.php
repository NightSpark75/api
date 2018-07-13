<?php
/*alice:
MPB生產報表
MPE QA QC
MPZ 倉管表單
*/
namespace App\Models\MPZ;

use Illuminate\Database\Eloquent\Model;

class MPZ_CATCHLOG extends Model
{
    //
    protected $connection = 'oracle';
    protected $table = "MPZ_CATCHLOG";
}
