<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/19 15:13
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\order;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Order;
use Modules\Admin\Services\admin\AdminService;
use Modules\Admin\Services\balance\BalanceRecord;
use Modules\Admin\Services\BaseService;
use Modules\Admin\Services\log\LogServices;
use Modules\Common\Models\Doer;
use Modules\Common\Models\overtruePay;

class OrderService extends BaseService
{
    public function index(array $data){
        $model = Order::query();
        $model = $this->queryCondition($model,$data,['nickName']);
        $list = $model->select('*')
            ->where('del',1)
            ->where('uniacid',$this->uniacid)
            ->with(['address:*'])
            ->with(['user:*'])
            ->with(['master:*'])
            ->with(['order_product:*'])
            ->orderBy('id','desc')
            ->paginate(10)
            ->toArray();
        $doer = new Doer();
        foreach ($list['data'] as $k=>$v){
            if($v['status']==11){
                $list['data'][$k]['refund_apply'] = DB::table('order_refund')->where('order_id',$v['id'])->where('del',1)->orderBy('id','desc')->first();
            }

            //订单日志
            $log = DB::table('log')->where('uniacid',$this->uniacid)
                ->where('cate',75)
                ->where('remark',$v['id'])
                ->orderBy('id','desc')
                ->get()
                ->toArray();

            foreach ($log as $k1=>$v1){
                $log[$k1]->create_at = date('Y/m/d H:i:s',$v1->create_at);

                if($doer->isJson($v1->detail)){
                    $remark_data = json_decode($v1->detail,true);
                    if(is_array($remark_data) && $remark_data && array_key_exists('pic',$remark_data)){
                        $pic = $remark_data['pic']&&$remark_data['pic']!=''?explode('&',$remark_data['pic']):[];
                        $log[$k1]->pic = $pic;
                    }
                    if(is_array($remark_data) && $remark_data && array_key_exists('remark',$remark_data)){
                        $remark = $remark_data['remark'];
                        $log[$k1]->remark_data = $remark;
                    }
                }
            }
            $list['data'][$k]['log'] = $log;

        }



        return $this->apiSuccess('',[
            'list'=>$list['data'],
            'total'=>$list['total']
        ]);
    }

    public function edit(array $data){
        $model = new Order();
        DB::beginTransaction();
        try{
            $_id = $this->commonCreateOrrUpdate($model,$data);
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            return $this->apiError('操作失败！');
        }
        return $this->apiSuccess();
    }

    public function del(int $id){
        $model = new Order();
        DB::beginTransaction();
        try{
            $insert_id = $this->commonDel($model,$id);
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            $this->apiError('删除失败！');
        }
        return $this->apiSuccess('',[]);
    }

    public function changeOrderMaster(array $data){
        $order = DB::table('order')->where('id',$data['order_id'])->first();
        $master = DB::table('master')->where('id',$data['master_id'])->first();
        if(!$master||!$order){return $this->apiError('无对应数据！');}
        if($order->master_id==$master->id){return $this->apiError('技师无更改！');}
        $today_start_time = strtotime(date('Y/m/d'));
        $time_status = DB::table('order')
            ->where('master_id',$data['master_id'])
            ->where('service_time',$order->service_time)
            ->where('status','>',0)
            ->where('status','<',6)
            ->where('create_at','>=',$today_start_time)
            ->where('create_at','<',$today_start_time+86399)
            ->where('del',1)
            ->first();
        if($time_status)return $this->apiError('该技师所选服务时间已约满，无法更换');

        $res = DB::table('order')->where('id',$data['order_id'])->update(['master_id'=>$data['master_id']]);

        return $this->apiSuccess('更改成功',$res);
    }

    public function refundOrder(array $data)
    {
        if(!array_key_exists('refund_money',$data))$data['refund_money'] = 0;

        $setting = DB::table('setting')->where('id',$this->uniacid)->first();
//        $setting->refund_destination = 2;

        $order = DB::table('order')->where('id',$data['order_id'])->first();
        if(!$order)return $this->apiError('订单不存在');
        if($order->status!=1 && $order->status!=2 && $order->status!=11)return $this->apiError('该订单所处的订单状态不支持退款操作');
        if($order->pay_fee<$data['refund_money'])return $this->apiError('退款金额不能大于支付金额！');
        $paylog = DB::table('paylog')->where('cate',1)->where('remark',$data['order_id'])->first();
        $order->pay_type==1?$order_no = $paylog->order_no:$order_no=$data['order_id'];
        //                echo "<pre>";print_r($order);echo "<pre>";die;

        if(array_key_exists('remark',$data)&&$data['remark']){$remark = $data['remark'];}else{$remark='';}
        $refund_no = date('YmdHis').rand(100000,999999).rand(100000,999999);
        $inc = [
            'uniacid'=>$this->uniacid,
            'order_id'=>$order->id,
            'refund_fee'=>$data['refund_money'],
            'remark'=>$remark,
            'order_no'=>$order_no,
            'order_status'=>$order->status,
            'refund_no'=>$refund_no,
            'create_at'=>time()
        ];

        $log = new LogServices();
        $log->uniacid = $this->uniacid;
        $adminData = (new AdminService())->getAdminInfo();
        $log->user_id = $adminData['admin_id'];
        $log->event = 'orderDo';
        $log->remark = $data['order_id'];

        if(array_key_exists('refund_id',$data)&&$data['refund_id']){
            $refund_id = $data['refund_id'];
            $order_refund = DB::table('order_refund')->where('id',$refund_id)->first();
            if(!$order_refund)return $this->apiError('退款申请不存在');
            if($data['check_status']==2){
                DB::table('order')->where('id',$order->id)->update(['status'=>$order_refund->order_status,'update_at'=>time()]);
                DB::table('order_refund')->where('id',$refund_id)->update(['check_status'=>2,'remark'=>$remark,'update_at'=>time()]);
                $log->content = '退款申请被拒绝:'.$remark;
                $log->saveLog();
                return $this->apiSuccess('已拒绝');
            }
            if($order_refund->check_status!=0)return $this->apiError('退款申请不符合条件');
        }
        else{
            if($data['check_status']==2){
                return $this->apiError('无意义操作');
            }
            $refund_id = DB::table('order_refund')->insertGetId($inc);
        }
//
        $inc['pay_fee'] = $order->pay_fee;
        if($data['check_status']==1){
            //直接退回账户余额
            if($setting->refund_destination==1 || $order->pay_type==2){
                $res = BalanceRecord::balanceChange($order->user_id,$data['refund_money'],12,1,$refund_id,$order->id);
                DB::table('order')->where('id',$order->id)->update(['status'=>12,'update_at'=>time()]);
                DB::table('order_refund')->where('id',$refund_id)->update(['status'=>1,'check_status'=>1,'update_at'=>time()]);
                $log->content = '退款成功,已退回至账户余额,金额'.$data['refund_money'].'元';
                $log->saveLog();
                return $this->apiSuccess('退款成功,已退回至账户余额');
            }
            else{
                if($order->pay_type==1){

                    $inc['out_trade_no'] = $paylog->out_trade_no;
                    $overtruePay = new overtruePay();
                    $overtruePay->uniacid = $this->uniacid;
                    $result = $overtruePay->refund($inc);

//                    echo "<pre>";print_r($result);echo "<pre>";die;

//                    $payModel = new \Modules\Common\Models\Pay();
//                    $payModel->uniacid = $this->uniacid;
//                    $result = $payModel->refund($inc);

                    if($result['code']==1){
                        DB::table('order')->where('id',$order->id)->update(['status'=>12,'update_at'=>time()]);
                        DB::table('order_refund')->where('id',$refund_id)->update(['status'=>1,'check_status'=>1,'transaction_id'=>$result['transaction_id'],'update_at'=>time()]);
                        $log->content = '退款成功,已原路返回,金额'.$data['refund_money'].'元';
                        $log->saveLog();
                        return $this->apiSuccess($result['msg']);
                    }else{
                        DB::table('order_refund')->where('id',$refund_id)->update(['status'=>3,'check_status'=>1,'result'=>$result['msg'],'update_at'=>time()]);
                        return $this->apiError($result['msg'],$result['data']);
                    }
                }
            }
        }




    }

}
