<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/6/19 06:52
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Api\Http\Requests\CarNumber;

class CarNumberController extends ApiController
{

    public function getNumber(Request $request){
        $data = DB::table('nuoche_lincec_car_number')->where('id',$request->input('id'))->first();
        return $this->apiSuccess('',$data);
    }
    public function createNumber(CarNumber $request){
        $inc = [
            'province'=>$request->input('province'),
            'number_a'=>$request->input('number_a'),
            'number_b'=>$request->input('number_b'),
            'number_c'=>$request->input('number_c'),
            'number_d'=>$request->input('number_d'),
            'number_e'=>$request->input('number_e'),
            'number_f'=>$request->input('number_f'),
            'enr'=>$request->input('enr',0),
            'number_type'=>$request->input('number_type',0),
            'str'=>$request->input('province').$request->input('number_a').$request->input('number_b').$request->input('number_c').$request->input('number_d').$request->input('number_e').$request->input('number_f')
        ];
        $q = DB::table('nuoche_lincec_car_number')->where('id',$request->input('id'))->first();
        if($q){
            $id = $q->id;
            $inc['update_at'] = time();
            DB::table('nuoche_lincec_car_number')->where('id',$id)->update($inc);
        }else{
            $id = DB::table('nuoche_lincec_car_number')->insertGetId($this->cInc($inc));
        }

        return $this->apiSuccess('',$id);
    }
}
