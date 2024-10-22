<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/6/17 16:19
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Api\Http\Requests\AgentRequest;

class AgentController extends ApiController
{

    public function index(){
        $data = $this->agent;
        if(!$data ||$data->status==2){
            $briefData =[
                'order_wait_start'=>'-',
                'order_ing'=>'-',
                'order_complete'=>'-',
                'order_cancel'=>'-',
            ];
            $data['calc'] = $briefData;
            $data['status'] = -1;

            return $this->apiSuccess('你还不是代理，无法访问该页面',$data);
        }
        if($data->status!=1){
            $briefData =[
                'order_wait_start'=>'-',
                'order_ing'=>'-',
                'order_complete'=>'-',
                'order_cancel'=>'-',
            ];
            $data->calc = $briefData;

            return $this->apiSuccess('',$data);
        }


        $order_wait_start = DB::table('order')
            ->join('address','order.address_id','=','address.id')
            ->where('order.status','=',1)
            ->where('address.province',$data->province)
            ->where('address.city',$data->city)
            ->where('address.district',$data->district)
            ->where('order.uniacid',$this->uniacid)
            ->where('order.del',1)->count();
        $order_ing = DB::table('order')
            ->join('address','order.address_id','=','address.id')
            ->where('order.status','=',2)
            ->where('address.province',$data->province)
            ->where('address.city',$data->city)
            ->where('address.district',$data->district)
            ->where('order.uniacid',$this->uniacid)
            ->where('order.del',1)->count();
        $order_complete = DB::table('order')
            ->join('address','order.address_id','=','address.id')
            ->where('order.status','=',3)
            ->where('address.province',$data->province)
            ->where('address.city',$data->city)
            ->where('address.district',$data->district)
            ->where('order.uniacid',$this->uniacid)
            ->where('order.del',1)->count();
        $order_cancel = DB::table('order')
            ->join('address','order.address_id','=','address.id')
            ->where('order.status','=',5)
            ->where('address.province',$data->province)
            ->where('address.city',$data->city)
            ->where('address.district',$data->district)
            ->where('order.uniacid',$this->uniacid)
            ->where('order.del',1)->count();

        $briefData =[

            'order_wait_start'=>$order_wait_start,
            'order_ing'=>$order_ing,
            'order_complete'=>$order_complete,
            'order_cancel'=>$order_cancel,

        ];
        $data->calc = $briefData;

        return $this->apiSuccess('',$data);
    }

    public function register(AgentRequest $request){

        $province = $request->input('province');
        $city = $request->input('city');
        $district = $request->input('district');

        $inc = [
            'name'=>$request->input('name'),
            'tel'=>$request->input('tel'),
            'remark'=>$request->input('remark'),
            'update_at'=>time()
        ];
        if(!$this->agent_id){
            $inc['province'] = $province;
            $inc['city'] = $city;
            $inc['district'] = $district;
            $inc['create_at'] = time();
            $id = DB::table('agent')->insertGetId($this->cInc($inc));
        }else{
            //地址更改，有变动
            if($this->agent->province!=$province||$this->agent->city!=$city||$this->agent->district!=$district){
                $inc['province'] = $province;
                $inc['city'] = $city;
                $inc['district'] = $district;
                $inc['status'] = 0;
            }
            DB::table('agent')->where('id',$this->agent_id)->update($inc);
        }

        return $this->apiSuccess('',$this->cInc($inc));
    }

}
