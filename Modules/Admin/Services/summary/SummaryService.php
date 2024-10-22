<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/19 17:08
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\summary;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\BaseService;

class SummaryService extends BaseService
{
    public function index(){

        $total_user = DB::table('user')->where('uniacid',$this->uniacid)->where('del',1)->count();
        $total_order = DB::table('order')->where('status','>',0)->where('uniacid',$this->uniacid)->where('del',1)->count();
        $total_order_money = DB::table('order')->where('status','>',0)->where('uniacid',$this->uniacid)->where('del',1)->sum('total_fee');
        $total_master = DB::table('master')->where('uniacid',$this->uniacid)->where('del',1)->count();

        $briefData =[
            'total_user'=>$total_user,
            'total_order'=>$total_order,
            'total_order_money'=>$total_order_money,
            'total_master'=>$total_master,
        ];

        return $this->apiSuccess('',['briefData'=>$briefData,'lineChart'=>['xAxis'=>[],'legend'=>[],'serial'=>[]]]);
    }
}
