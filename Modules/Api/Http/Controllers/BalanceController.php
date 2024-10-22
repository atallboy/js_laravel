<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/22 21:54
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\balance\BalanceRecord;
use Modules\Common\Models\Pay;

class BalanceController extends ApiController
{
    public function record(Request $request){

        $pay = new Pay();
        $pay->uniacid = $this->uniacid;
        $pay->queryPaylogByCircle();

        $query = DB::table('balance_record')->where('user_id',$this->user_id)->where('del',1)
            ->orderBy('id','desc');
        if($request->input('record_cate')=='distribute_fee'){
            $query->where('cate','>',30)->where('cate','<',34);
        }
        if($request->input('record_cate')=='agent_fee'){
            $query->where('cate','=',4);
        }

        if($request->input('record_cate')=='master_fee'){
            $query->whereIn('cate', [3, 5,6, 7,8]); 
        }
         $list = $query->get();
        foreach ($list as $k=>$v){
            $list[$k]->create_at = date('Y-m-d H:i:s',$v->create_at);
            $list[$k]->msg = BalanceRecord::checkBalanceTypeDesc($v->cate);
        }
        return $this->apiSuccess('',$list);
    }

    public function withdrawal(Request $request){
        $money = $request->input('money');
        if($this->user->balance<$money)return $this->apiError('余额不足，无法提现');
        $inc = [
            'name'=>$request->input('name'),
            'tel'=>$request->input('tel'),
            'money'=>$money,
            'zfb_account'=>$request->input('zfb_account'),
        ];
        $_id = DB::table('withdrawal_record')->insertGetId($this->cInc($inc));
        $res = BalanceRecord::balanceChange($this->user_id,$money,21,2,$_id);
        if($res['code']!=1)return $this->apiError($res['msg']);
        return $this->apiSuccess('');
    }

    public function withdrawalRecord (){
        $list = DB::table('withdrawal_record')->where('user_id',$this->user_id)->where('del',1)
            ->orderBy('id','desc')
            ->get();
        foreach ($list as $k=>$v){
            $list[$k]->create_at = date('Y-m-d H:i:s',$v->create_at);
            if($v->update_at)date('Y-m-d H:i:s',$v->update_at);
        }
        return $this->apiSuccess('',$list);
    }

}
