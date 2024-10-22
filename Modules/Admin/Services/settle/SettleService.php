<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/8/3 21:59
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\settle;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\balance\BalanceRecord;
use Modules\Admin\Services\BaseService;

class SettleService extends BaseService
{
    public function initSettle(){
        $list = DB::table('settle_solution')
            ->where('status',1)
            ->where('del',1)
            ->where('uniacid',$this->uniacid)
            ->get();
//        echo "<pre>";print_r(date('w'));echo "<pre>";
//        echo "<pre>";print_r($list);echo "<pre>";
        foreach ($list as $k=>$v){
            if($v->settle_time==1){

            }
            if($v->settle_time==2){
                $end_time = strtotime(date('Y/m/d'))-(date('w')-1)*86400-1;
                $start_time = $end_time-86400*7+1;
                $_time = date('Ymd',$start_time).'-'.date('Ymd',$end_time);

                $q = DB::table('settle_time')
                    ->where('settle_solution_id',$v->id)->where('uniacid',$this->uniacid)->where('_time',$_time)->where('del',1)->first();
                if(!$q){
                    DB::table('settle_time')->insert(
                        [
                            'uniacid'=>$this->uniacid,
                            'settle_solution_id'=>$v->id,
                            '_time'=>$_time,
                            'start_time'=>$start_time,
                            'end_time'=>$end_time,
                            'create_at'=>time()]
                    );
                }

//                echo "<pre>";print_r(date('Y/m/d H:i:s',$start_time));echo "<pre>";
//                echo "<pre>";print_r(date('Y/m/d H:i:s',$end_time));echo "<pre>";
            }
            if($v->settle_time==3){
                $end_time = strtotime(date('Y/m/d'))-(date('d')-1)*86400-1;
                $start_time = strtotime(date('Y-m',$end_time).'-01');
                $_time = date('Ymd',$start_time);
                $q = DB::table('settle_time')
                    ->where('settle_solution_id',$v->id)->where('uniacid',$this->uniacid)->where('_time',$_time)->where('del',1)->first();
                if(!$q){
                    DB::table('settle_time')->insert(
                        [
                            'uniacid'=>$this->uniacid,
                            'settle_solution_id'=>$v->id,
                            '_time'=>$_time,
                            'start_time'=>$start_time,
                            'end_time'=>$end_time,
                            'create_at'=>time()]
                    );
                }
            }
            if($v->settle_time==5){
                $end_time = strtotime((date('Y')-1).'-12-31')+86399;
                $start_time = strtotime((date('Y')-1).'-01-01');
                $_time = date('Ymd',$start_time);
                $q = DB::table('settle_time')
                    ->where('settle_solution_id',$v->id)->where('uniacid',$this->uniacid)->where('_time',$_time)->where('del',1)->first();
                if(!$q){
                    DB::table('settle_time')->insert(
                        [
                            'uniacid'=>$this->uniacid,
                            'settle_solution_id'=>$v->id,
                            '_time'=>$_time,
                            'start_time'=>$start_time,
                            'end_time'=>$end_time,
                            'create_at'=>time()]
                    );
                }
            }
        }
    }

    public function settle($master,$id){
        $item = DB::table('settle_record')->where('id',$id)->first();
        $settle_time = DB::table('settle_time')->where('id',$item->settle_time_id)->first();
        $master_id = $master->id;
//        $master_id = 2;
        $order = DB::table('order')
            ->where('master_id',$master_id)
            ->where('status',4)
            ->where('complete_at','>=',$settle_time->start_time)
            ->where('complete_at','<=',$settle_time->end_time)
            ->where('del',1)
            ->get();
//        echo "<pre>";print_r($master_id);echo "<pre>";
//        echo "<pre>";print_r($order);echo "<pre>";die;
        $order_money = 0;
        $order_num = 0;
        $jiazong_order_money = 0;
        $jiazhong_order_num = 0;
        foreach ($order as $k=>$v){
            $order_num+=1;
            $order_money+=$v->product_fee;
            if($v->is_jiazhong){
                $jiazhong_order_num+=1;
                $jiazong_order_money+=DB::table('jiazhong_record')->where('order_id',$v->id)->where('status',1)->sum('product_money');
            }
        }
        $inc = [
            'achieve_money'=>$order_money,
            'order_num'=>$order_num,
            'jiazhong_achieve_money'=>$jiazong_order_money,
            'jiazhong_order_num'=>$jiazhong_order_num,
            'status'=>1,
            'update_at'=>time()
        ];
        $ladder = DB::table('settle_solution_ladder')->where('settle_solution_id',$settle_time->settle_solution_id)
            ->where('fee_type','=',1)
            ->where('min','<=',$order_money)
            ->where('max','>',$order_money)
            ->where('status',1)
            ->where('del',1)
            ->first();
        if($ladder){
            $reward_money = round($ladder->percent*$order_money/100,2);
            $inc['reward_money'] = $reward_money;
            BalanceRecord::balanceChange($master->user_id,$reward_money,7,1,$item->id);
        }
        $jiazhong_ladder = DB::table('settle_solution_ladder')->where('settle_solution_id',$settle_time->settle_solution_id)
            ->where('fee_type','=',2)
            ->where('min','<=',$jiazong_order_money)
            ->where('max','>',$jiazong_order_money)
            ->where('status',1)
            ->where('del',1)
            ->first();
        if($jiazhong_ladder){
            $reward_money = round($jiazhong_ladder->percent*$jiazong_order_money/100,2);
            $inc['jiazhong_reward_money'] = $reward_money;
            BalanceRecord::balanceChange($master->user_id,$reward_money,8,1,$item->id);
        }

        $res = DB::table('settle_record')->where('id',$item->id)->update($inc);
        return $res;
    }

}
