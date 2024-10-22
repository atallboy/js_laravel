<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/20 22:58
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Api\Services\OrderServices;

class CouponController extends ApiController
{

    public function exchange(Request $request){
        $redeem_code = $request->input('redeem_code');
        $data = DB::table('coupon')
            ->where('status',1)
            ->where('del',1)
            ->where('redeem_code',$redeem_code)
            ->first();
        if(!$data)return $this->apiError('兑换码错误');

        $exchange_record = DB::table('coupon_exchange_record')
            ->where('user_id',$this->user_id)
            ->where('coupon_id',$data->id)
            ->where('redeem_code',$redeem_code)
            ->where('status',1)
            ->where('del',1)
            ->first();
//        if($exchange_record)return $this->apiError('你已经兑换过，不可重复兑换');

        $incExchange = [
            'coupon_id'=>$data->id,
            'redeem_code'=>$redeem_code
        ];
        $exchange_id = DB::table('coupon_exchange_record')->insertGetId($this->cInc($incExchange));

         $inc = [
             'coupon_id'=>$data->id,
             'remark'=>$exchange_id,
         ];
         if($data->valid_time_type==1){
             $inc['valid_start_time'] = $data->valid_start_time;
             $inc['valid_end_time'] = $data->valid_end_time;
         }
         if($data->valid_time_type==2){
             $inc['valid_start_time'] = time();
             $inc['valid_end_time'] = time()+$data->valid_day*86400;
         }
         DB::table('coupon_record')->insert($this->cInc($inc));

        return $this->apiSuccess();
    }

    public function record(Request $request){

        $money=1000000;
        if($ids = $request->input('ids')){
            $idsArr = explode('&',$ids);
            $money = (new OrderServices())->calcItemTotalMoney($idsArr);
        }
        $cate = $request->input('cate');
        $use_range = $request->input('use_range');
        $list = DB::table('coupon_record')
            ->join('coupon','coupon.id','=','coupon_record.coupon_id')
            ->where('coupon_record.user_id',$this->user_id)
            ->where('coupon_record.status',1)
            ->where('coupon.amount','<=',$money)
            ->where('coupon_record.del',1)
            ->where('coupon.del',1)
            ->select(['coupon.*',
                'coupon_record.valid_start_time',
                'coupon_record.valid_end_time',
                'coupon_record.status as coupon_record.status',
                'coupon_record.id as coupon_record_id',
                'coupon_record.create_at as time'
            ]);
        if($use_range=='submit_order'){
            $list->where('coupon_record.valid_start_time','<',time())->where('coupon_record.valid_end_time','>',time());
        }
        if($cate)$list->where('cate',$cate);
        $list = $list->get();
        foreach ($list as $k=>$v){
            if($v->type==2){
                if($v->minimum>$money){
                    unset($list[$k]);continue ;
                }
            }
            $list[$k]->valid_start_time = date('Y/m/d H:i',$v->valid_start_time);
            $list[$k]->valid_end_time = date('Y/m/d H:i',$v->valid_end_time);
            $list[$k]->time = date('m/d H:i',$v->time);
        }

        return $this->apiSuccess('',$list);
    }

    public function detail(Request $request){
        $id = $request->input('id');
        $car = DB::table('coupon')->where('id',$id)->first();
        return $this->apiSuccess('',$car);
    }

    public function index(Request $request)
    {
        $list = DB::table('coupon')
            ->where('user_id',$this->user_id)
            ->where('del',1)
            ->get();
        return $this->apiSuccess('',$list);
    }

    public function del(Request $request){
        $id = $request->input('id');
        $data = DB::table('coupon')->where('id',$id)->update(['del'=>0,'delete_at'=>time()]);
        return $this->apiSuccess('',$data);
    }
}
