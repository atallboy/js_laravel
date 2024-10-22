<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/9/30 22:32
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Common\Models\Doer;

class CityController extends ApiController
{
    public function index(Request $request)
    {
//        echo "<pre>";print_r($access_token);echo "<pre>";die;
        $list = DB::table('city')
            ->where('uniacid',$this->uniacid)
            ->where('status',1)
            ->where('del',1);

        $list = $list->get()->toArray();

        return $this->apiSuccess('',$list);
    }

}
