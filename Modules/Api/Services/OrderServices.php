<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/22 08:08
 * @Description: 版权所有
 */

namespace Modules\Api\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\balance\BalanceRecord;
use Modules\Admin\Services\distribute\DistributeMchService;
use Modules\Admin\Services\log\LogServices;
use Modules\Admin\Services\master\MaterService;
use Modules\Common\Models\Common;
use Modules\Common\Models\PrivacyTel;
use Modules\Common\Models\Sms;
use Modules\Common\Models\SubscribeMessage;

class OrderServices
{
    public $uniacid;
    public $order_id;
    public $user_id;

    public static function getOrderStatusCode($status){
        $arr = [
            'waitPay'=>0,
            'waitTaking'=>1,
            'startService'=>2,  //拆分
            'finish'=>3,
            'feedback'=>4,
            'cancel'=>5,
            'applyRefund'=>11,
            'refund'=>12,
            'taking'=>20,
            'departure'=>21,
            'arrival'=>22,
            'serviceStart'=>23,
            'serviceComplete'=>24,

            'eva'=>100,
        ];
        return $arr[$status];
    }

    public static function getOrderCodeMsg($status){
        $arr = [
            0=>'待支付',
            1=>'待接单',//已支付
            2=>'服务中',
            3=>'订单完成',
            4=>'售后/待反馈',
            5=>'已取消',
            11=>'申请退款',
            12=>'已退款',
            20=>'技师接单',//待出发
            21=>'技师出发',//技师出发中 待到达
            22=>'技师到达',//技师到达  待开始服务
            23=>'开始服务', //
            24=>'服务完成',
            25=>'技师拒绝接单',
            100=>'服务评价',
        ];
        if($status===false)return $arr;
        return $arr[$status];
    }

    public static function getCodeMsgByStr($str){
        $code = self::getOrderStatusCode($str);
        return self::getOrderCodeMsg($code);
    }

    public function calcItemTotalMoney($idArr){
        $money=0;
        foreach ($idArr as $v){
            $money+=DB::table('item')->where('id',$v)->value('price');
        }
        return $money;
    }

    //处理优惠券
    public function doOrderPayed($id){
        $order = DB::table('order')->where('id',$id)->first();
        if($order->status==1){
            if($order->coupon_id){
                DB::table('coupon_record')->where('id',$order->coupon_id)->update(['status'=>2,'update_at'=>time()]);
            }
        }
    }


    public function orderHavePay($cate,$id,$nonStr){
        $setting =   DB::table('setting')->where('uniacid',$this->uniacid)->where('del',1)->first();
        $order = DB::table('order')->where('id',$id)->first();
        $pay_log = DB::table('paylog')->where('cate',1)->where('remark',$id)->first();
        if($nonStr!=md5($cate.'123'.$id))return false;
        if($cate==1){
            if(!$order)return false;
            if($order->status!=0)return false;
            $master_auto_taking = DB::table('master')->where('id',$order->master_id)->value('auto_taking');
            $master_auto_taking?$status=20:$status=1;
            DB::table('order')->where('id',$id)->update(['status'=>$status,'update_at'=>time()]);
            if($status==20){
                $log = new LogServices();
                $log->uniacid = $this->uniacid;
                $log->user_id = $this->user_id;
                $log->event = 'orderDo';
                $log->remark = $id; 
                $log->content = '技师接单';
                $log->saveLog();
            }


            $log = new LogServices();
            $log->uniacid = $this->uniacid;
            $log->user_id = $order->user_id;
            $log->event = 'orderDo';
            $log->remark = $id;
            $log->content = '订单微信支付成功';
            $log->saveLog();
            (new OrderServices())->doOrderPayed($id);
            $subscribe = new SubscribeMessage();
            $subscribe->user_id = $order->user_id;
            $subscribe->order_id = $id;

            $subscribe->uniacid = $this->uniacid;
            $master = DB::table('master')->where('id',$order->master_id)->first();
            $master_user = DB::table('user')->where('id',$master->user_id)->first();
            $subscribe_list = DB::table('subscribe_message')->where('uniacid',$this->uniacid)->where('status',1)->where('trigger_event',1)->get()->toArray();
            foreach ($subscribe_list as $k=>$v){
                $param = $subscribe->getSubscribeDataValue($v,$master_user->openid);
                $res = $subscribe->sendSubscribeMessage($param);
            }

//            echo "<pre>";print_r($setting);echo "<pre>";die;
            if($setting->privacy_tel_notify_status){
                $param = [
                    "templateArgs"=>(object)[],
                    "calleeNumber"=>$master->tel,
                ];
                $res = (new PrivacyTel())->telDo('notify',$param,$this->uniacid);


                $log = new LogServices();
                $log->uniacid = $this->uniacid;
                $log->user_id = $order->user_id;
                $log->event = 'privacyNotify';
                $log->remark = $id;
                $log->content = json_encode($res);
                $log->saveLog();

            }

            $sms_setting = DB::table('sms_setting')->where('uniacid',$this->uniacid)->where('del',1)->first();
            if($sms_setting->order_notice){
                $phone_number = $master->tel;
                $appcode = $sms_setting->appcode;
                $smsSignId = $sms_setting->smsSignId;
                $param = "**order_no**%3A";
                if($pay_log)$param.=$pay_log->order_no;
                $templateId = $sms_setting->template_notice_id;
                $res = (new Sms())->sendGySms($appcode,$phone_number,$param,$smsSignId,$templateId);
                $res['phone'] = $phone_number;
                $res['param'] = $param;

                $log = new LogServices();
                $log->uniacid = $this->uniacid;
                $log->user_id = $this->user_id;
                $log->event = 'sendSmsOrderNotice';
                $log->remark = $phone_number;
                $log->content = json_encode($res);
                $log->saveLog();
            }

        }
        if($cate==2){
            $r = DB::table('recharge_record')->where('id',$id)->first();
            if($r->status!=0)return false;
            DB::table('recharge_record')->where('id',$r->id)->update(['status'=>1,'update_at'=>time()]);
            BalanceRecord::balanceChange($r->user_id,$r->money,1,1,$id);
        }
        if($cate==3){

//            $order = DB::table('order')->where('id',$id)->first();
//            DB::table('order')->where('id',$id)->update(['status'=>1,'update_at'=>time()]);
            $jz_record = DB::table('jiazhong_record')->where('id',$id)->orderBy('id','desc')->first();
            if($jz_record->status!=0)return false;
            DB::table('jiazhong_record')->where('id',$jz_record->id)->update(['status'=>1,'update_at'=>time()]);
            DB::table('order')->where('id',$jz_record->order_id)->update(['is_jiazhong'=>1,'update_at'=>time()]);

            $master_performance_id = (new MaterService())->getMasterPerformanceIdByMasterId($order->master_id);
            $master_performance = DB::table('master_performance')->where('id',$master_performance_id)->where('del',1)->first();
            if(!$master_performance)return false;
            $ud = [
                'update_at'=>time()
            ];
            $ud['jiazhong_order'] = $master_performance->jiazhong_order+1;
            $ud['jiazhonglv'] = round($ud['jiazhong_order']/$ud['complete_order']*100,1);
            DB::table('master_performance')->where('id',$master_performance->id)
                ->update($ud);

        }
        return 200;
    }


    public function doOrderFinish($id){

        $order = DB::table('order')->where('id',$id)->first();
        $order_settle = DB::table('order_settle')->where('order_id',$id)->first();
        $user = DB::table('user')->where('id',$order->user_id)->first();
        $setting = DB::table('setting')->where('uniacid',$order->uniacid)->where('del',1)->first();

        $master_id = $order->master_id;


        if(!$order_settle){

            $master_percent = $setting->master_percent;
            $master_performance_id = (new MaterService())->getMasterPerformanceIdByMasterId($master_id);

            //技师分成
            $master = DB::table('master')->where('id',$master_id)->first();
            $master_profit = round($order->product_fee*$master_percent/100,2);
            BalanceRecord::balanceChange($master->user_id,$master_profit,3,1,$id);
            (new Common())->incrementValue('master_performance',$master_performance_id,'service_fee',$master_profit);

            //出行费
            BalanceRecord::balanceChange($master->user_id,$order->travel_fee,5,1,$id);
            (new Common())->incrementValue('master_performance',$master_performance_id,'travel_fee',$order->travel_fee);

            //加钟费
            $jiazhong_profit = DB::table('jiazhong_record')->where('order_id',$id)->where('status',1)->sum('product_money');
            $jiazhong_profit = round($jiazhong_profit*$master_percent/100,2);
            BalanceRecord::balanceChange($master->user_id,$jiazhong_profit,6,1,$id);
            (new Common())->incrementValue('master_performance',$master_performance_id,'jiazhong_fee',$jiazhong_profit);

            //技师经营表
            (new Common())->incrementValue('master_performance',$master_performance_id,'complete_order');

            //区域代理分成
            $address = DB::table('address')->where('id',$order->address_id)->first();
            $agent_list = DB::table('agent')
                ->join('user','agent.user_id','=','user.id')
                ->where('agent.status',1)
                ->where('agent.del',1)
                ->where('user.del',1)
                ->where('agent.province',$address->province)
                ->where('agent.city',$address->city)
                ->where('agent.district',$address->district)
                ->select(['agent.*'])
                ->get();
            $agent_num =  count($agent_list);
            $agent_profit = $agent_total_profit = 0;
            if($agent_num>0){
                $agent_total_profit = $order->product_fee*$setting->agent_percent/100;
                $agent_profit = round($agent_total_profit/$agent_num,2);
                foreach ($agent_list as $v){
                    BalanceRecord::balanceChange($v->user_id,$agent_profit,4,1,$id);
                    (new Common())->incrementValue('agent',$v->id,'order_num');
                    (new Common())->incrementValue('agent',$v->id,'order_money',$agent_profit);
//                    DB::table('agent')->where('id',$v->id)->increment('order_num');
//                    DB::table('agent')->where('id',$v->id)->increment('order_money',$agent_profit);
                }
            }

            //分销  先看有没有自定义分销，再看有没有二维码分销，最后看系统分销
            $DModel = new DistributeMchService();
            if($user->pre_id){
                $setting_distribute = DB::table('setting_distribute')->where('uniacid',$order->uniacid)->where('del',1)->first();
                if($setting_distribute->status==1){
                    $first_user = DB::table('user')->where('id',$user->pre_id)->first();

                    $percent_first = $this->getUserDistributeFit($order->uniacid,$user->pre_id,'percent_first');

//                    $percent_first = $setting_distribute->percent_first;
//                    //查找该用户有没有自定义分销
//                    $distributeMch = (new UserService())->getUserDistributeMchInfo($user->pre_id);
//                    if($distributeMch){
//                        if($distributeMch->open_percent){
//                            $percent_first = $distributeMch->percent_first;
//                        }else{
//                            $distributeQrcode = (new UserService())->getUserDistributeQrcode($user->pre_id);
//                            if($distributeQrcode){
//                                $percent_first = $distributeQrcode->percent_first;
//                            }
//                        }
//                    }

                    $first_fee = round($percent_first*$order->product_fee/100,2);
                    BalanceRecord::balanceChange($first_user->id,$first_fee,31,1,$id,json_encode(['percent'=>$setting_distribute->percent_first,'product_fee'=>$order->product_fee,'pre_id'=>$user->pre_id]));
                    //分销商经营数据
                    $fid = $DModel->getDistributeMchPerformanceIdByUserId($first_user->id);
                    if($fid){
                        (new Common())->incrementValue('distribute_mch_performance',$fid,'total_order');
                        (new Common())->incrementValue('distribute_mch_performance',$fid,'total_order_first');
                        (new Common())->incrementValue('distribute_mch_performance',$fid,'total_fee',$first_fee);
                        (new Common())->incrementValue('distribute_mch_performance',$fid,'total_fee_first',$first_fee);
                    }


                    if($first_user->pre_id && $setting_distribute->level>1){
                        $second_user = DB::table('user')->where('id',$first_user->pre_id)->first();
//                        $percent_second = $setting_distribute->percent_second;
//                        //查找该用户有没有自定义分销
//                        $secondDistributeMch = (new UserService())->getUserDistributeMchInfo($first_user->pre_id);
//                        if($secondDistributeMch && $secondDistributeMch->open_percent){
//                            $percent_second = $secondDistributeMch->percent_second;
//                        }
                        $percent_second = $this->getUserDistributeFit($order->uniacid,$first_user->pre_id,'percent_second');

                        $second_fee = round($percent_second*$order->product_fee/100,2);
                        BalanceRecord::balanceChange($second_user->id,$second_fee,32,1,$id,json_encode(['percent'=>$setting_distribute->percent_second,'product_fee'=>$order->product_fee,'pre_id'=>$first_user->pre_id]));
                        //分销商经营数据
                        $fid = $DModel->getDistributeMchPerformanceIdByUserId($second_user->id);
                        if($fid){
                            (new Common())->incrementValue('distribute_mch_performance',$fid,'total_order');
                            (new Common())->incrementValue('distribute_mch_performance',$fid,'total_order_second');
                            (new Common())->incrementValue('distribute_mch_performance',$fid,'total_fee',$second_fee);
                            (new Common())->incrementValue('distribute_mch_performance',$fid,'total_fee_second',$second_fee);
                        }

                        if($second_user->pre_id && $setting_distribute->level>2){
//                            $percent_third = $setting_distribute->percent_third;
//                            //查找该用户有没有自定义分销
//                            $thirdDistributeMch = (new UserService())->getUserDistributeMchInfo($second_user->pre_id);
//                            if($thirdDistributeMch && $thirdDistributeMch->open_percent){
//                                $percent_third = $thirdDistributeMch->percent_third;
//                            }

                            $percent_third = $this->getUserDistributeFit($order->uniacid,$second_user->pre_id,'percent_third');

                            $third_fee = round($percent_third*$order->product_fee/100,2);
                            BalanceRecord::balanceChange($second_user->pre_id,$third_fee,33,1,$id,json_encode(['percent'=>$setting_distribute->percent_third,'product_fee'=>$order->product_fee,'pre_id'=>$second_user->pre_id]));
                            //分销商经营数据
                            $fid = $DModel->getDistributeMchPerformanceIdByUserId($second_user->pre_id);
                            if($fid){
                                (new Common())->incrementValue('distribute_mch_performance',$fid,'total_order');
                                (new Common())->incrementValue('distribute_mch_performance',$fid,'total_order_third');
                                (new Common())->incrementValue('distribute_mch_performance',$fid,'total_fee',$third_fee);
                                (new Common())->incrementValue('distribute_mch_performance',$fid,'total_fee_third',$third_fee);
                            }

                        }
                    }
                }
            }

            $order_settle_inc = [
                'uniacid'=>$order->uniacid,
                'order_id'=>$id,
                'master_fee'=>$master_profit,
                'agent_fee'=>$agent_profit,
                'agent_avg_fee'=>$agent_total_profit,
                'create_at'=>time()
            ];
            DB::table('order_settle')->insert($order_settle_inc);


            $udp = ['status'=>OrderServices::getOrderStatusCode('finish'),'complete_at'=>time()];
            DB::table('order')->where('id',$id)->update($udp);

        }
    }


    public function getUserDistributeFit($uniacid,$user_id,$percent_level){
        $setting_distribute = DB::table('setting_distribute')->where('uniacid',$uniacid)->where('del',1)->first();
        $percent = $setting_distribute->{$percent_level};
        //查找该用户有没有自定义分销
        $distributeMch = (new UserService())->getUserDistributeMchInfo($user_id);
        if($distributeMch){
            if($distributeMch->open_percent){
                $percent = $distributeMch->{$percent_level};
            }else{
                $distributeQrcode = (new UserService())->getUserDistributeQrcode($user_id);
                if($distributeQrcode){
                    $percent = $distributeQrcode->{$percent_level};
                }
            }
        }
        return $percent;
    }



//    订单进程记录,暂未启用
    public function recordOrderProcess($act,$content){

        $order = DB::table('order')->where('id',$this->order_id)->where('del',1)->first();
        $inc = [
            'uniacid'=>$order->uniacid,
            'user_id'=>$this->user_id,
            'order_id'=>$this->order_id,
            'cate'=>$act,
            'content'=>$content,
            'create_at'=>time(),
        ];
        DB::table('order_process')->where('id',$order->coupon_id)->insert($inc);

    }

}
