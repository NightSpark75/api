<?php
/**
 * 揀貨處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/01/22
 * @since 1.0.0 spark: build basic function
 * 
 */
namespace App\Http\Controllers\Native;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\Native\PickingRepository;

/**
 * Class PickingController
 *
 * @package App\Http\Controllers
 */
class PickingController extends Controller
{
    //
    private $picking;

    /**
     * construct
     * 
     * @param JwtRepository $auth
     * @return void
     */
    public function __construct(PickingRespository $picking) {
        $this->picking = $picking;
    }

    public function getPickingList() {
        $result = 
    }

    public function startPicking() {

    }

    public function endPicking() {

    }
}
