<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/6/12 08:45
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Support\Facades\DB;

class RechargeController extends BaseController
{
    function index()
    {
        $list = DB::table('nuoche_lincec_user')->limit(10)->get();
        $total = DB::table('nuoche_lincec_user')->count();

        $list = [];
        $total = 0;

        return response(['code'=>20000,'data'=>['list'=>$list,'total'=>$total]]);
    }

    public function edit(){
        return response(['code'=>20000,'message'=>'1','data'=>[]]);
    }

    public function del(){
        return response(['code'=>20000,'message'=>'1','data'=>[]]);
    }
}
