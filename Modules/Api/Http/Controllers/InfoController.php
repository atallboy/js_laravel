<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/20 11:59
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Common\Models\Common;

class InfoController extends ApiController
{
    function settingInfo()
    {
        $data = DB::table('setting')->where('uniacid',$this->uniacid)->first();
        $data->eva_tag = explode('|',$data->eva_tag);
//        $city_arr = ['不限城市'];
//        $data->city_arr = array_merge($city_arr,explode('|',$data->city_arr));

        $city_arr = [];
        $city_list = DB::table('city')
            ->where('uniacid',$this->uniacid)
            ->where('status',1)
            ->where('del',1)->get();
        foreach ($city_list as $k=>$v){
            $city_arr[] = $v->name;
        }
        $data->city_arr = $city_arr;

        if($data->open_service_link){
            if($this->user){
                $data->service_link = str_replace('name=','name='.$this->user->nickName,$data->service_link);
            }
            if($this->token){
                $data->service_link = str_replace('desc=','name='.$this->token,$data->service_link);
            }
        }
        $data->host = 'https://'.$_SERVER['HTTP_HOST'];


        //订单选项卡
        $order_cate = [
            ['id'=>101,'active'=>'tab-item-active','name'=>'全部订单'],
            ['id'=>0,'name'=>'待支付','active'=>''],
            ['id'=>1,'name'=>'待接单','active'=>''],
            ['id'=>20,'name'=>'已接单','active'=>''],
            ['id'=>21,'name'=>'待出发','active'=>''],
            ['id'=>22,'name'=>'待到达','active'=>''],
            ['id'=>23,'name'=>'待开始','active'=>''],
            ['id'=>24,'name'=>'待完成','active'=>''],
            ['id'=>3,'name'=>'已完成','active'=>''],
            ['id'=>100,'name'=>'已评价','active'=>''],
            ['id'=>11,'name'=>'申请退款','active'=>''],
            ['id'=>12,'name'=>'已退款','active'=>''],
        ];

        $data->order_cate = $order_cate;


//        $data->open_auth_phone = 1;
        return $this->apiSuccess('',$data);
    }

    function bannerList(Request $request)
    {
        $banner = DB::table('banner')->where('cate',1)->where('del',1)->where('status',1)->where('uniacid',$this->uniacid)->orderBy('_sort','desc')->get();
        foreach ($banner as $k=>$v){
            $banner[$k]->url_param = Common::getNavigateParam($v);
        }
        $toast = DB::table('banner')->where('cate',2)->where('del',1)->where('status',1)->where('uniacid',$this->uniacid)->orderBy('_sort','desc')->get();
        foreach ($toast as $k=>$v){
            $toast[$k]->url_param = Common::getNavigateParam($v);
        }

        $token = $request->session()->all();


        return $this->apiSuccess('',['banner'=>$banner,'toast'=>$toast,'token'=>$token]);
    }

}
