<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/6/19 09:53
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Api\Http\Requests\Car;

class CarController extends ApiController
{

    public function car(Request $request){
        $id = $request->input('id');
        $car = DB::table('nuoche_lincec_car')->where('id',$id)->first();
        $car->car_number = $car_number = DB::table('nuoche_lincec_car_number')->where('id',$car->car_number_id)->first();
        return $this->apiSuccess('',$car);
    }

    public function index(Request $request)
    {
        $list = DB::table('nuoche_lincec_car')
            ->join('nuoche_lincec_car_number','nuoche_lincec_car.car_number_id','=','nuoche_lincec_car_number.id')
            ->where('nuoche_lincec_car.uniacid',$this->uniacid)->where('nuoche_lincec_car.del',1)
            ->select(['nuoche_lincec_car.*','nuoche_lincec_car_number.str'])
            ->get();
        return $this->apiSuccess('',$list);
    }

    public function createCar(Car $request){
        $id = $request->input('id');
        $car = DB::table('nuoche_lincec_car')->where('id',$id)->first();
        $inc = [
            'car_number_id'=>$request->input('car_number_id'),
            'serial_number'=>$request->input('serial_number'),
            'privacy_tel'=>$request->input('privacy_tel'),
            'name'=>$request->input('name'),
            'gender'=>$request->input('gender'),
            'brand'=>$request->input('brand'),
            'model'=>$request->input('model'),
            'answer_way'=>$request->input('answer_way'),
            'wechat'=>$request->input('wechat'),
            'message'=>$request->input('message'),
            'time'=>$request->input('time'),
            'tel'=>$request->input('tel'),
            'time2'=>$request->input('time2'),
            'tel2'=>$request->input('tel2'),
            'tel3'=>$request->input('tel3'),
            'time3'=>$request->input('time3'),
            'update_at'=>time()
        ];
        if($car){
            $id = $car->id;
            DB::table('nuoche_lincec_car')->where('id',$id)->update($inc);
        }else{
            $id = DB::table('nuoche_lincec_car')->insertGetId($this->cInc($inc));
        }

        return $this->apiSuccess('',$id);
    }

    function unbind(Request $request)
    {
        $id = $request->input('id');
        $res = DB::table('nuoche_lincec_car')->where('id',$id)->where('user_id',$this->user_id)->update(['del'=>0,'delete_at'=>time()]);
        return $this->apiSuccess('',$res);
    }

}
