<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/9 22:58
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Master extends ApiController
{
    public function detail(Request $request){
        $id = $request->input('id');
        $car = DB::table('master')->where('id',$id)->first();
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

    public function edit(Request $request){
        $id = $request->input('id',0);
        $car = DB::table('master')->where('id',$id)->first();
        $inc = [
            'name'=>$request->input('name'),
            'tel'=>$request->input('tel'),
            'age'=>$request->input('age'),
            'gender'=>$request->input('gender'),
            'city'=>$request->input('city'),
            'update_at'=>time()
        ];
        if($car){
            $id = $car->id;
            DB::table('master')->where('id',$id)->update($inc);
        }else{
            $id = DB::table('master')->insertGetId($this->cInc($inc));
        }
 
        return $this->apiSuccess('',$id);
    }
}
