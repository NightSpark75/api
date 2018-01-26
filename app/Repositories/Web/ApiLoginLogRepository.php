<?php
/**
 * 登錄歷程資源庫
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/01/26
 * @since 1.0.0 spark: build database I/O function 
 * 
 */
namespace App\Repositories\Web;

use Exception;
use JWTAuth;
use App\Traits\Sqlexecute;
use App\Traits\Oracle;
use App\Interfaces\RepositoryInterface;
use App\Repositories\Repository;
use DB;

/**
 * Class PickingRepository
 *
 * @package App\Repositories
 */
class ApiLoginLogRepository extends Repository
{   
    use Sqlexecute;
    use Oracle;

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\Models\Web\ApiLoginLog';
    }

    public function add($user, $ip) {
        DB::insert("
            insert into api_login_log (user_id, ip)
                values (:user_id, :ip)
        ", ['user_id' => $user, 'ip' => $ip]);
    }

    public function refresh($user, $ip) 
    {
        $this->model->where('token', $token)->update(['change', $refresh]);
        $this->addLog($user, $refresh);
    }
}