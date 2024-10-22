<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/9/18 22:09
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Faker\Extension\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Modules\Admin\Services\log\LogServices;
use Modules\Common\Models\PrivacyTel;

class PrivacyTelController extends ApiController
{


    public function Axb(Request $request)
    {

        $id = $request->input('order_id');
        $user_type = $request->input('user_type');
        $data = DB::table('order')
            ->join('master','order.master_id','=','master.id')
            ->join('address','order.address_id','=','address.id')
            ->where('order.id',$id)
            ->select(['order.*','master.name as master_name','master.tel as master_tel','address.name','address.tel','address.city','address.district','address.address'])
            ->first();
        if(!$data)return $this->apiError('订单不存在');
        //用户拨打师傅
        if($this->user_id==$data->user_id && $user_type=='user'){
            $caller = $data->tel;
            $bindNumberB = $data->master_tel;
        }
        //师傅拨打用户
        elseif($this->master_id==$data->master_id && $user_type=='master'){
            $caller = $data->master_tel;
            $bindNumberB = $data->tel;
        }else{
            return $this->apiError('无权限');
        }

        $param = [
            "bindNumberA" => $caller,
            "bindNumberB" => $bindNumberB,
            "callRec" => 1,
            "maxBindingTime" => 600,
        ];

        $res = (new PrivacyTel())->telDo('axb',$param,$this->uniacid);
        $log = new LogServices();
        $log->uniacid = $this->uniacid;
        $log->user_id = $this->user_id;
        $log->event = 'privacyBind';
        $log->remark = $id;
        $log->content = json_encode($res);
        $log->saveLog();

        if(array_key_exists('result',$res) && $res['result']=='000000'){
              return $this->apiSuccess('success',$res['middleNumber']);
        }else{
            $msgA = '号码A格式错误';
            $msgB = '号码B格式错误';
            return $this->apiError('隐私电话获取失败:'.$res['message']);
        }

        echo "<pre>";print_r($param);echo "<pre>";
        echo "<pre>";print_r($res);echo "<pre>";
    }








    public function testUnbind()
    {
        $appId = "936fdf9f5572456fa4b7a795cb037879";
        $token = "4c9a2e181bf84f40b15ac6343c39ce21";
        $timestamp = round(microtime(true) * 1000);
        $sig = md5($appId . $token . $timestamp);

        $response = Http::withHeaders([
            'Authorization' => base64_encode($appId . ":" . $timestamp),
            'Accept' => 'application/json',
        ])->post($this->baseUrl . "/voice/1.0.0/notify/$appId/$sig", [
            "middleNumber" => "13262714450",  // 已绑定的小号号码
            "bindNumberA" => "18701424736",  // 已经绑定的真实号码telA
        ]);

        return response()->json([
            'status' => $response->status(),
            'response' => $response->json(),
        ]);
    }
}
