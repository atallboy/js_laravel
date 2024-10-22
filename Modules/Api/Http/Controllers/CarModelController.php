<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/6/18 15:18
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CarModelController extends ApiController
{
    public function index(Request $request){

        $validator = Validator::make($request->only(['brand']),['brand' => 'required',],['brand.required'=>'品牌不可缺少']);
        if ($validator->fails()) { return $this->apiError($this->validateErrorMsg($validator)); }

        $brand = DB::table('nuoche_lincec_brand')->where('name',$request->input('brand'))->where('del',1)->first();
        $list = DB::table('nuoche_lincec_model')->where('brand_id',$brand->id)->where('del',1)->get();

        return $this->apiSuccess('',$list);
    } 
}
