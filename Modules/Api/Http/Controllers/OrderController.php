<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/18 12:19
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\balance\BalanceRecord;
use Modules\Admin\Services\log\LogServices;
use Modules\Admin\Services\order\OrderService;
use Modules\Api\Http\Requests\OrderRequest;
use Modules\Api\Services\MasterService;
use Modules\Api\Services\OrderServices;
use Modules\Admin\Services\order\OrderService as AdminOrderServices;
use Modules\Common\Models\Common;
use Modules\Common\Models\Doer;
use Modules\Common\Models\overtruePay;
use Modules\Common\Models\Pay;
use Modules\Common\Models\PrivacyTel;
use Modules\Common\Models\Sms;
use Modules\Common\Models\SubscribeMessage;

class OrderController extends ApiController
{

    public function upgrade(Request $request){
        $id = $request->input('id');
        $item_id = $request->input('item_id');
        $order_product = DB::table('order_product')->where('id',$id)->first();
        $item = DB::table('item')->where('id',$item_id)->first();

        $price = $item->price-$order_product->total_price;

        $inc = [
            'order_id'=>$id,
            'item_id'=>$item_id,
            'price'=>$price
        ];
        $_id = DB::table('order_upgrade')->insertGetId($this->cInc($inc));

        $log = new LogServices();
        $log->uniacid = $this->uniacid;
        $log->user_id = $this->user_id;
        $log->event = 'orderLog';
        $log->remark = $id;
        $log->content = '订单升级';
        $log->detail = json_encode(['order_product'=>$order_product,'item'=>$item]);
        $log->saveLog();

        $overtruePay = new overtruePay();
        $overtruePay->uniacid = $this->uniacid;
        $param = ['user_id'=>$this->user_id,'openid'=>$this->user->openid,'money'=>$inc['price'],'description'=>'升级订单','remark'=>$_id,'cate'=>4,'agent_cate'=>$this->agent_cate];
        $result = $overtruePay->createOrder($param);

        $result['id'] = $id;

        return $this->apiSuccess('',$result);
    }

    public function jiazhong(Request $request){
        $id = $request->input('id');
        $item_id = $request->input('item_id');
        $data = DB::table('order')
            ->join('master','order.master_id','=','master.id')
            ->join('address','order.address_id','=','address.id')
            ->where('order.id',$id)
            ->select(['order.*','master.name as master_name','address.name','address.tel','address.city','address.district','address.address'])
            ->first();

        $item = DB::table('item')->where('id',$item_id)->first();

        $inc = [
            'order_id'=>$id,
            'item_id'=>$item_id,
            'product_money'=>$item->price
        ];
        $jiazhong_id = DB::table('jiazhong_record')->insertGetId($this->cInc($inc));

        $overtruePay = new overtruePay();
        $overtruePay->uniacid = $this->uniacid;
        $param = ['user_id'=>$this->user_id,'openid'=>$this->user->openid,'money'=>$inc['product_money'],'description'=>'加钟订单','remark'=>$jiazhong_id,'cate'=>3,'agent_cate'=>$this->agent_cate];
        $result = $overtruePay->createOrder($param); //overtruePay

        $result['id'] = $id;

        return $this->apiSuccess('',$result);
    }

    public function detail(Request $request){
        $setting =   DB::table('setting')->where('uniacid',$this->uniacid)->where('del',1)->first();
        $user_type = $request->input('user_type');
        $id = $request->input('id');
        $data = DB::table('order')
            ->join('master','order.master_id','=','master.id')
            ->join('address','order.address_id','=','address.id')
            ->where('order.id',$id)
            ->select(['order.*','master.name as master_name','master.tel as master_tel','address.name','address.tel','address.city','address.district','address.address'])
            ->first();
        $order_product = DB::table('order_product')
            ->join('item','item.id','=','order_product.item_id')
            ->where('order_product.order_id',$id)
            ->select(['order_product.*','item.name','item.pic'])
            ->get();
        $item = DB::table('item')->where('id',$order_product[0]->item_id)->first();
        $data->item = $item;
        $data->address = DB::table('address')->where('id',$data->address_id)->first();
        $data->order_product = $order_product;
        $data->create_at = date('Y-m-d H:i:s',$data->create_at);

        $data->order_settle = DB::table('order_settle')->where('order_id',$data->id)->first();

        //加钟记录
        $jiazhong_record = DB::table('jiazhong_record')
            ->join('item','jiazhong_record.item_id','=','item.id')
            ->where('jiazhong_record.order_id',$id)->where('jiazhong_record.status',1)
            ->select(['jiazhong_record.*','item.name'])
            ->get();
        $doer = new Doer();
        $data->jiazhong_record = $doer->dealData($jiazhong_record);


        $is_eva = DB::table('eva_record')->where('order_id',$id)->where('del',1)->first();
        $is_eva?$data->is_eva=1:$data->is_eva=0;

        //订单日志
        $log = DB::table('log')->where('uniacid',$this->uniacid)
            ->where('cate',75)
            ->where('remark',$id)
            ->orderBy('id','asc')
            ->get()
            ->toArray();

        $step_list = [
            'taking'=>['name'=>'taking', 'title'=>'技师接单','text'=>'待接单','master_text'=>'立即接单','act_role'=>'master', 'is_show'=>1,'statusCode'=>'','btnStatus'=>1,'time'=>'','pic'=>'','remark'=>'','is_modify'=>0],
            'departure'=>['name'=>'departure','title'=>'技师出发','text'=>'待出发','master_text'=>'立即出发','act_role'=>'master','is_show'=>0,'statusCode'=>'','btnStatus'=>0,'time'=>'','pic'=>'','remark'=>'','is_modify'=>0],
            'arrival'=>['name'=>'arrival','title'=>'技师到达','text'=>'待到达','master_text'=>'已到达','act_role'=>'master','is_show'=>0,'statusCode'=>'','btnStatus'=>0,'time'=>'','pic'=>'','remark'=>'','is_modify'=>0],
            'serviceStart'=>['name'=>'serviceStart','title'=>'开始服务','text'=>'待服务','master_text'=>'开始服务','act_role'=>'master','is_show'=>0,'statusCode'=>'','btnStatus'=>0,'time'=>'','pic'=>'','remark'=>'','is_modify'=>0],
            'serviceComplete'=>['name'=>'serviceComplete','title'=>'完成服务','text'=>'待完成','master_text'=>'完成服务','act_role'=>'master','is_show'=>0,'statusCode'=>'','btnStatus'=>0,'time'=>'','pic'=>'','remark'=>'','is_modify'=>0],
            'finish'=>['name'=>'finish','title'=>'确认完成','text'=>'确认完成','master_text'=>'待确认完成','act_role'=>'user','is_show'=>0,'statusCode'=>'','btnStatus'=>0,'time'=>'','pic'=>'','remark'=>'','is_modify'=>0],
            'eva'=>['name'=>'eva','title'=>'服务评价','text'=>'去评价','is_show'=>0,'master_text'=>'待评价','act_role'=>'user','statusCode'=>'','btnStatus'=>0,'time'=>'','pic'=>'','remark'=>'','is_modify'=>0],
        ];
        $doer = new Doer();
        $remain_time = 0;
        foreach ($log as $k=>$v){
            $log[$k]->create_at = date('Y/m/d H:i:s',$v->create_at);
            $pic = [];
            $remark = '';
            if($doer->isJson($v->detail)){
                $remark_data = json_decode($v->detail,true);
                if(is_array($remark_data) && $remark_data && array_key_exists('pic',$remark_data)){
                    $pic = $remark_data['pic']&&$remark_data['pic']!=''?explode('&',$remark_data['pic']):[];
                    $log[$k]->pic = $pic;
                }
                if(is_array($remark_data) && $remark_data && array_key_exists('remark',$remark_data)){
                    $remark = $remark_data['remark'];
                    $log[$k]->remark_data = $remark;
                }
            }
            $c = $v->content;
            $time = $log[$k]->create_at;

            if($c==OrderServices::getCodeMsgByStr('taking')){
                $step_list['taking']['time'] = $time;
                $step_list['taking']['text'] = '已接单';
                $step_list['taking']['master_text'] = '已接单';
                $step_list['taking']['btnStatus'] = 0;
            }

            if(in_array($data->status,[3,20,21,22,23,24,100])){
                $step_list['departure']['is_show'] = 1;
                if($step_list['departure']['is_modify']==0)$step_list['departure']['btnStatus'] = 1;
                if($c==OrderServices::getCodeMsgByStr('departure')){
                    $step_list['departure']['time'] = $time;
                    $step_list['departure']['text'] = '已出发';
                    $step_list['departure']['master_text'] = '我已出发';
                    $step_list['departure']['pic'] = $pic;
                    $step_list['departure']['remark'] = $remark;
                    $step_list['departure']['btnStatus'] = 0;
                    $step_list['departure']['is_modify']=1;
                }
            }


            if(in_array($data->status,[3,21,22,23,24,100])){
                $step_list['arrival']['is_show'] = 1;
                if($step_list['arrival']['is_modify']==0)$step_list['arrival']['btnStatus'] = 1;
                if($c==OrderServices::getCodeMsgByStr('arrival')){
                    $step_list['arrival']['btnStatus'] = 0;
                    $step_list['arrival']['time'] = $time;
                    $step_list['arrival']['text'] = '已到达';
                    $step_list['arrival']['master_text'] = '我已到达';
                    $step_list['arrival']['pic'] = $pic;
                    $step_list['arrival']['remark'] = $remark;
                    $step_list['arrival']['is_modify']=1;
                }
            }

            if(in_array($data->status,[3,22,23,24,100])){
                $step_list['serviceStart']['is_show'] = 1;
                if($step_list['serviceStart']['is_modify']==0)$step_list['serviceStart']['btnStatus'] = 1;
                if($c==OrderServices::getCodeMsgByStr('serviceStart')){
                    $remain_time = (strtotime($time)+$item->long_time*60)-time();
                    $step_list['serviceStart']['btnStatus'] = 0;
                    $step_list['serviceStart']['time'] = $time;
                    $step_list['serviceStart']['text'] = '服务中';
                    $step_list['serviceStart']['master_text'] = '服务中';
                    $step_list['serviceStart']['pic'] = $pic;
                    $step_list['serviceStart']['remark'] = $remark;
                    $step_list['serviceStart']['is_modify']=1;
                }
            }

            if(in_array($data->status,[3,23,24,100])){
                $step_list['serviceComplete']['is_show'] = 1;
                if($step_list['serviceComplete']['is_modify']==0)$step_list['serviceComplete']['btnStatus'] = 1;
                if($c==OrderServices::getCodeMsgByStr('serviceComplete')){
                    $step_list['serviceComplete']['btnStatus'] = 0;
                    $step_list['serviceComplete']['time'] = $time;
                    $step_list['serviceComplete']['text'] = '已完成服务';
                    $step_list['serviceComplete']['master_text'] = '已完成服务';
                    $step_list['serviceComplete']['pic'] = $pic;
                    $step_list['serviceComplete']['remark'] = $remark;
                    $step_list['serviceComplete']['is_modify']=1;
                }
            }

            if(in_array($data->status,[3,24,100])){
                $step_list['finish']['is_show'] = 1;
                if($step_list['finish']['is_modify']==0)$step_list['finish']['btnStatus'] = 1;
                if($c==OrderServices::getCodeMsgByStr('finish')){
                    $step_list['finish']['btnStatus'] = 0;
                    $step_list['finish']['time'] = $time;
                    $step_list['finish']['text'] = '已完成';
                    $step_list['finish']['master_text'] = '已完成';
                    $step_list['finish']['pic'] = $pic;
                    $step_list['finish']['remark'] = $remark;
                    $step_list['finish']['is_modify']=1;
                }
            }

            if(in_array($data->status,[3,100])){
                $step_list['eva']['is_show'] = 1;
                if($step_list['eva']['is_modify']==0)$step_list['eva']['btnStatus'] = 1;
                if($c==OrderServices::getCodeMsgByStr('eva')){
                    $step_list['eva']['btnStatus'] = 0;
                    $step_list['eva']['time'] = $time;
                    $step_list['eva']['text'] = '已评价';
                    $step_list['eva']['master_text'] = '已评价';
                    $step_list['eva']['pic'] = $pic;
                    $step_list['eva']['remark'] = $remark;
                    $step_list['eva']['is_modify']=1;
                }
            }


        }

        $remain_time>0?$data->remain_time = $remain_time:$data->remain_time = 0;

        $step = [];
        foreach ($step_list as $k=>$v){
            $step[] = $v;
        }

        $data->step_list = $step;

        $data->log = $log;

        $data->is_privacy_tel = 0;
        if($setting->privacy_tel_status){
            $data->is_privacy_tel = 1;


            if($user_type=='master'){
                $data->address->tel = '******';
                $data->tel = '******';
            }
            if($user_type=='user'){
                $data->master_tel = '******';
            }
        }

        return $this->apiSuccess('',$data);
    }

    public function orderDo(Request $request){
        $id = $request->input('id');
        $op = $request->input('op');
        $pic = $request->input('pic');
        $remark = $request->input('remark');
        $log = new LogServices();
        $log->uniacid = $this->uniacid;
        $log->user_id = $this->user_id;
        $log->event = 'orderDo';
        $log->remark = $id;


        if($op=='startService'){  //已被拆分    'departure'=>21, 'arrival'=>22,  'serviceStart'=>23,   'serviceComplete'=>24,
            $pic = $request->input('pic');
            DB::table('order')->where('id',$id)->update(['status'=>OrderServices::getOrderStatusCode('startService'),'start_service_time'=>time()]);
            $log->content = '订单开始服务';
            $log->detail = $pic;
            $log->saveLog();
        }

        if($op=='taking'){
            DB::table('order')->where('id',$id)->update(['status'=>OrderServices::getOrderStatusCode('taking')]);
            $log->content = '技师接单';
            $log->saveLog();
        }

        if($op=='refuseTaking'){
            $reason = $request->input('reason');
            DB::table('order')->where('id',$id)->update(['status'=>OrderServices::getOrderStatusCode('refuseTaking')]);
            $log->content = '技师拒绝接单';
            $log->detail = $reason;
            $log->saveLog();
        }

        if($op=='departure'){
            DB::table('order')->where('id',$id)->update(['status'=>OrderServices::getOrderStatusCode('departure'),'start_service_time'=>time()]);
            $log->content = '技师出发';
            $log->detail = json_encode(['pic'=>$pic,'remark'=>$remark]);
            $log->saveLog();
        }

        if($op=='arrival'){
            DB::table('order')->where('id',$id)->update(['status'=>OrderServices::getOrderStatusCode('arrival')]);
            $log->content = '技师到达';
            $log->detail = json_encode(['pic'=>$pic,'remark'=>$remark]);
            $log->saveLog();
        }

        if($op=='serviceStart'){
            DB::table('order')->where('id',$id)->update(['status'=>OrderServices::getOrderStatusCode('serviceStart')]);
            $log->content = '开始服务';
            $log->detail = json_encode(['pic'=>$pic,'remark'=>$remark]);
            $log->saveLog();
        }

        if($op=='serviceComplete'){
            DB::table('order')->where('id',$id)->update(['status'=>OrderServices::getOrderStatusCode('serviceComplete')]);
            $log->content = '服务完成';
            $log->detail = json_encode(['pic'=>$pic,'remark'=>$remark]);
            $log->saveLog();
        }

        if($op=='cancel'){
            DB::table('order')->where('id',$id)->update(['status'=>OrderServices::getOrderStatusCode('cancel')]);
            $log->content = '取消订单';
            $log->saveLog();
        }

        if($op=='finish'){
            (new OrderServices())->doOrderFinish($id);
            $log->content = '订单完成';
            $log->saveLog();
        }

        if($op=='refund'){
            $money = $request->input('money');
            $reason = $request->input('reason');
            $order = DB::table('order')->where('id',$id)->where('del',1)->first();
            if(!$order)return $this->apiError('订单不存在');
            if($order->user_id!=$this->user_id)return $this->apiError('无权操作该订单');
            if($order->status!=1 && $order->status!=2)return $this->apiError('目前该订单状态无法退款');
            $order_refund = DB::table('order_refund')->where('order_id',$id)->where('status',0)->where('del',1)->first();
            if($order_refund)return $this->apiError('有退款申请未处理，无法继续申请');
            $paylog = DB::table('paylog')->where('cate',1)->where('remark',$id)->first();
            if($order->pay_fee<$money)return $this->apiError('退款金额不能大于支付金额！');
            $inc = [
                'uniacid'=>$this->uniacid,
                'order_id'=>$order->id,
                'type'=>1,
                'apply_fee'=>$money,
                'reason'=>$reason,
                'order_no'=>$paylog?$paylog->order_no:'',
                'refund_no'=>date('YmdHis').rand(100000,999999).rand(100000,999999),
                'order_status'=>$order->status,
                'check_status'=>0,
                'status'=>0,
                'create_at'=>time()
            ];
            $_id = DB::table('order_refund')->insertGetId($inc);

            DB::table('order')->where('id',$id)->update(['status'=>OrderServices::getOrderStatusCode('applyRefund')]);
            $log->content = '订单申请退款，退款金额：'.$money.'，退款理由：'.$reason;
            $log->saveLog();

//            $res = (new AdminOrderServices())->refundOrder($data);
//            $res = json_decode($res,true);
//            if($res['code']!=20000){
//                return $this->apiError($res['message']);
//            }
            return $this->apiSuccess();
        }

        return $this->apiSuccess('');
    }

    public function index(Request $request)
    {
        $status = $request->input('status');
        $u = $request->input('user');

        $list = DB::table('order')
            ->join('master','order.master_id','master.id')
            ->join('address','order.address_id','address.id')
            ->where('order.del',1)
            ->orderBy('order.id','desc')
            ->select(['order.*','master.name as master_name']);

        if($u=='master'){
            $list->where('order.master_id',$this->master_id);
        }elseif ($u=='agent'){
            $list->where('address.district',$this->agent->district);
        }else{
            $list->where('order.user_id',$this->user_id);
        }

        if($status==101){
            $list->where('order.status','!=',5);
        }else{
            $list->where('order.status','=',$status);
        }

        $list = $list->get()->toArray();
        $statusMsgArr = (OrderServices::getOrderCodeMsg(false));
        foreach ($list as $k=>$v){
            $list[$k]->status_msg = $statusMsgArr[$v->status];
            $order_product = DB::table('order_product')
                ->join('item','item.id','=','order_product.item_id')
                ->where('order_product.order_id',$v->id)
                ->select(['order_product.*','item.name'])
                ->get();
            $list[$k]->order_product = $order_product;
            $list[$k]->order_settle = DB::table('order_settle')->where('order_id',$v->id)->first();
        }

        return $this->apiSuccess('',$list);
    }

    public function create(OrderRequest $request){

        $master_id = $request->input('master_id');

        $setting =   DB::table('setting')->where('uniacid',$this->uniacid)->where('del',1)->first();
        $master = DB::table('master')->where('id',$master_id)->first();

        $coupon_record_id = $request->input('coupon_id');
        $coupon = DB::table('coupon_record')
            ->join('coupon','coupon.id','=','coupon_record.coupon_id')
            ->where('coupon_record.user_id',$this->user_id)
            ->where('coupon_record.status',1)
            ->where('coupon_record.del',1)
            ->where('coupon.del',1)
            ->where('coupon_record.id',$coupon_record_id)
            ->select(['coupon.*','coupon_record.id as coupon_record_id'])
            ->first();



        if($coupon_record_id&&!$coupon)return $this->apiError('优惠券不存在');

        $inc = [
            'pay_type'=>$request->input('pay_type'),
            'address_id'=>$address_id = $request->input('address_id'),
            'coupon_id'=>$coupon_record_id,
            'master_id'=>$master_id,
            'service_time'=>$request->input('service_time'),
            'remark'=>$request->input('remark',''),
            'travel_type'=>$request->input('travel_type',0),
            'travel_fee'=>$request->input('travel_expense',0),
        ];

        if($master->travel_expense && $master->qibujia>0 && !($inc['travel_fee']>0))return $this->apiError('出行费计算错误，请刷新页面或重新选择地址');


        if(!(new MasterService())->checkMasterTimeFree($inc['master_id'],$inc['service_time']))return $this->apiError('所选服务时间已约满，请更换');

        $address = DB::table('address')->where('id',$address_id)->first();
        if(!$address)return $this->apiError('地址无效，请重新添加');

        $id = DB::table('order')->insertGetId($this->cInc($inc));

//        echo "<pre>";print_r($inc);echo "<pre>";die;
        $order_detail = [
            'uniacid'=>$this->uniacid,
            'order_id'=>$id,
            'name'=>$address->name,
            'tel'=>$address->tel,
            'province'=>$address->province,
            'city'=>$address->city,
            'district'=>$address->district,
            'address'=>$address->address,
            'latitude'=>$address->latitude,
            'longitude'=>$address->longitude,
            'remark'=>$address->remark,
            'create_at'=>time()
        ];
        DB::table('order_detail')->insert($order_detail);

        $log = new LogServices();
        $log->uniacid = $this->uniacid;
        $log->user_id = $this->user_id;
        $log->event = 'orderDo';
        $log->remark = $id;
        $log->content = '订单创建成功';
        $log->saveLog();

        $total_fee = 0;
        $product_fee = 0;
        $snap_name = $snap_pic = '';
        $ids = explode('&',$request->input('ids'));
        foreach ($ids as $k=>$v){
            $c = explode('=',$v);
            $item = DB::table('item')->where('id',$c[0])->first();
            if(!$snap_name){
                $snap_name=$item->name;
                $snap_pic=$item->pic;
            }
            $p = [
                'uniacid'=>$this->uniacid,
                'order_id'=>$id,
                'item_id'=>$c[0],
                'num'=>$c[1],
                'total_price'=>$item->price*$c[1],
                'create_at'=>time()
            ];
            $total_fee += $p['total_price'];
            $product_fee += $p['total_price'];
            DB::table('order_product')->insert($p);
        }

        //出行费
        if($inc['travel_type']){
           $total_fee+=$inc['travel_fee'];
        }

        $pay_fee = $total_fee;
        $order_update = ['pay_fee'=>$pay_fee,'total_fee'=>$total_fee,'product_fee'=>$product_fee];
        if(count($ids)>1)$snap_name.='等'.count($ids).'件商品';
        $order_update['snap_name'] = $snap_name;
        if($coupon){
            if($coupon->amount>$pay_fee)return $this->apiError('优惠券无法使用');
            $order_update['coupon_id'] = $coupon->coupon_record_id;
            $order_update['coupon_fee'] = $coupon->amount;
            $pay_fee-=$coupon->amount;
        }


//        echo "<pre>";print_r($order_update);echo "<pre>";die;
        DB::table('order')->where('id',$id)->update($order_update);

        $result = [];
        if($inc['pay_type']==1){
//            $payModel = new \Modules\Common\Models\Pay();
//            $payModel->uniacid = $this->uniacid;
//            $payModel->agent_cate = $this->agent_cate;
//            $payModel->user_id = $this->user_id;
//            $payModel->cate = 1;
//            $payModel->order_data = ['money'=>$pay_fee,'description'=>$snap_name,'remark'=>$id];
//            $result = $payModel->createPay();

            $overtruePay = new overtruePay();
            $overtruePay->uniacid = $this->uniacid;
            $param = ['user_id'=>$this->user_id,'openid'=>$this->user->openid,'money'=>$pay_fee,'description'=>$snap_name,'remark'=>$id,'cate'=>1,'agent_cate'=>$this->agent_cate];
            $result = $overtruePay->createOrder($param); //overtruePay

        }
        if($inc['pay_type']==2){
            $res = BalanceRecord::balanceChange($this->user_id,$order_update['pay_fee'],20,2,$id);
            if($res['code']!=1)return $this->apiError($res['msg']);

            $master_auto_taking = DB::table('master')->where('id',$inc['master_id'])->value('auto_taking');
            $master_auto_taking?$status=20:$status=1;
            if($status==20){
                $log = new LogServices();
                $log->uniacid = $this->uniacid;
                $log->user_id = $this->user_id;
                $log->event = 'orderDo';
                $log->remark = $id;
                $log->content = '技师接单';
                $log->saveLog();
            }

            DB::table('order')->where('id',$id)->update(['status'=>$status]);

            $log = new LogServices();
            $log->uniacid = $this->uniacid;
            $log->user_id = $this->user_id;
            $log->event = 'orderDo';
            $log->remark = $id;
            $log->content = '订单余额支付成功';
            $log->saveLog();

            (new OrderServices())->doOrderPayed($id);
            $subscribe = new SubscribeMessage();
            $subscribe->user_id = $this->user_id;
            $subscribe->order_id = $id;
            $subscribe->uniacid = $this->uniacid;


            $master_user = DB::table('user')->where('id',$master->user_id)->first();
            $subscribe_list = DB::table('subscribe_message')->where('uniacid',$this->uniacid)->where('trigger_event',1)->where('status',1)->where('del',1)->get()->toArray();
            foreach ($subscribe_list as $k=>$v){
                $param = $subscribe->getSubscribeDataValue($v,$master_user->openid);
                $res = $subscribe->sendSubscribeMessage($param);
            }


            if($setting->privacy_tel_notify_status){
                $param = [
                    "templateArgs"=>(object)[],
                    "calleeNumber"=>$master->tel,
                ];
                $res = (new PrivacyTel())->telDo('notify',$param,$this->uniacid);

                $log = new LogServices();
                $log->uniacid = $this->uniacid;
                $log->user_id = $this->user_id;
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
                $param = "**order_no**%3A--";
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

        $result['id'] = $id;

        return $this->apiSuccess('',$result);
    }

    public function updateOrder(Request $request){
        return $this->apiSuccess('','');
        $cate = $request->input('cate');
        $id = $request->input('id');
        $order = DB::table('order')->where('id',$id)->first();
        if($cate==1){
            DB::table('order')->where('id',$id)->update(['status'=>1,'update_at'=>time()]);
            $log = new LogServices();
            $log->uniacid = $this->uniacid;
            $log->user_id = $this->user_id;
            $log->event = 'orderDo';
            $log->remark = $id;
            $log->content = '订单微信支付成功';
            $log->saveLog();
            (new OrderServices())->doOrderPayed($id);
            $subscribe = new SubscribeMessage();
            $subscribe->user_id = $this->user_id;
            $subscribe->order_id = $id;

            $subscribe->uniacid = $this->uniacid;
            $master = DB::table('master')->where('id',$order->master_id)->first();
            $master_user = DB::table('user')->where('id',$master->user_id)->first();
            $subscribe_list = DB::table('subscribe_message')->where('uniacid',$this->uniacid)->where('status',1)->where('trigger_event',1)->get()->toArray();
            foreach ($subscribe_list as $k=>$v){
                $param = $subscribe->getSubscribeDataValue($v,$master_user->openid);
                $res = $subscribe->sendSubscribeMessage($param);
            }

        }
        if($cate==2){
            $r = DB::table('recharge_record')->where('id',$id)->first();
            DB::table('recharge_record')->where('id',$r->id)->update(['status'=>1,'update_at'=>time()]);
            BalanceRecord::balanceChange($r->user_id,$r->money,1,1,$id);
        }
        if($cate==3){

//            $order = DB::table('order')->where('id',$id)->first();
//            DB::table('order')->where('id',$id)->update(['status'=>1,'update_at'=>time()]);
            $jz_record = DB::table('jiazhong_record')->where('order_id',$id)->orderBy('id','desc')->first();
            DB::table('jiazhong_record')->where('id',$jz_record->id)->update(['status'=>1,'update_at'=>time()]);
            DB::table('order')->where('id',$jz_record->order_id)->update(['is_jiazhong'=>1,'update_at'=>time()]);
        }
        return $this->apiSuccess('ok');
    }


}
