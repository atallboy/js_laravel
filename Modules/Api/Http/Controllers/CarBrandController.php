<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/6/18 10:40
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Support\Facades\DB;

class CarBrandController extends ApiController
{
    public function index(){
        $list = DB::table('nuoche_lincec_brand')->where('del',1)->get();

        return $this->apiSuccess('',$list);
    }
}
