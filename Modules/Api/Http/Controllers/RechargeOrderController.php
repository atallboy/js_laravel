<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/6/19 17:16
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Common\Models\overtruePay;

class RechargeOrderController extends ApiController
{

    function create(Request $request)
    {

//        return $this->apiError('$res');

        $validator = Validator::make($request->only(['recharge_id']),['recharge_id' => 'required',],['recharge_id.required'=>'套餐不可缺少']);
        if ($validator->fails()) { return $this->apiError($this->validateErrorMsg($validator)); }

//        $r = DB::table('recharge_solution')->where('id',$request->input('recharge_id'))->where('uniacid',$this->uniacid)->where('del',1)->where('status',1)->first();
//        if(!$r)return $this->apiError();
        $recharge_id = $request->input('recharge_id');
        $money = ($request->input('money'));
//        if($recharge_id==1)$money=0.01;
        $inc = [
            'recharge_id'=>$recharge_id,
            'money'=>$money
        ];
        $_id = DB::table('recharge_record')->insertGetId($this->cInc($inc));


        $overtruePay = new overtruePay();
        $overtruePay->uniacid = $this->uniacid;
        $param = ['user_id'=>$this->user_id,'openid'=>$this->user->openid,'money'=>$money,'description'=>'充值'.$money.'元','remark'=>$_id,'cate'=>2,'agent_cate'=>$this->agent_cate];
        $result = $overtruePay->createOrder($param); //overtruePay

//        return $this->apiSuccess('',$res);

        //yansongdaPay
//        $payModel = new \Modules\Common\Models\Pay();
//        $payModel->uniacid = $this->uniacid;
//        $payModel->agent_cate = $this->agent_cate;
//        $payModel->user_id = $this->user_id;
//        $payModel->cate = 2;
//        $payModel->order_data = ['money'=>$money,'description'=>'充值'.$money.'元','remark'=>$_id];
//        $result = $payModel->createPay();

        $result['id']  = $_id;



        return $this->apiSuccess('',$result);
    }

}
