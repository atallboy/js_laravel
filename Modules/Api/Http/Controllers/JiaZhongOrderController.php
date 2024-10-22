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
use Modules\Api\Http\Requests\OrderRequest;
use Modules\Api\Services\OrderServices;
use Modules\Common\Models\Doer;
use Modules\Common\Models\SubscribeMessage;

class JiaZhongOrderController extends ApiController
{

    public function jiazhongorder(Request $request){

      $res =   Db::table('jishi_lincec_jiazhong_record')->get();

      if($res){

          return ['code'=>0,'messgae'=>'获取加钟订单成功','data'=>$res];

      }else{

          return ['code'=>400,'messgahe'=>'获取加钟订单失败'];
      }
//        $id = $request->input('id');
//        $data = DB::table('order')
//            ->join('master','order.master_id','=','master.id')
//            ->join('address','order.address_id','=','address.id')
//            ->where('order.id',$id)
//            ->select(['order.*','master.name as master_name','address.name','address.tel','address.city','address.district','address.address'])
//            ->first();
//
//        $inc = [
//            'order_id'=>$id,
//            'product_money'=>$data->product_fee
//        ];
//        $jiazhong_id = DB::table('jiazhong_record')->insertGetId($this->cInc($inc));
//
//        $payModel = new \Modules\Common\Models\Pay();
//        $payModel->user_id = $this->user_id;
//        $payModel->uniacid = $this->uniacid;
//        $payModel->agent_cate = 'gzh';
//        $payModel->cate = 3;
//        $payModel->order_data = ['money'=>$inc['product_money'],'description'=>'订单订单','remark'=>$jiazhong_id];
//        $result = $payModel->createPay();
//        $result['id'] = $id;
//
//        return $this->apiSuccess('',$result);
//
//


    }



}
