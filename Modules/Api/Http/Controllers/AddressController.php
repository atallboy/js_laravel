<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/17 21:23
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends ApiController
{
    public function detail(Request $request){
        $id = $request->input('id');
        $car = DB::table('address')->where('id',$id)->first();
        return $this->apiSuccess('',$car);
    }

    public function index(Request $request)
    {
        $list = DB::table('address')
            ->where('user_id',$this->user_id)
            ->where('del',1)
            ->orderBy('id','desc')
            ->get();
        return $this->apiSuccess('',$list);
    }

    public function edit(Request $request){
        $id = $request->input('id',0);
        $data = DB::table('address')->where('id',$id)->first();
        $inc = [
            'name'=>$request->input('name'),
            'tel'=>$request->input('tel'),
            'province'=>$request->input('province'),
            'city'=>$request->input('city'),
            'district'=>$request->input('district'),
            'address'=>$request->input('address'),
            'latitude'=>$request->input('latitude'),
            'longitude'=>$request->input('longitude'),
            'remark'=>$request->input('remark'),
            'update_at'=>time()
        ];
        if($data){
            $id = $data->id;
            DB::table('address')->where('id',$id)->update($inc);
        }else{
            $id = DB::table('address')->insertGetId($this->cInc($inc));
        }
        return $this->apiSuccess('',$id);
    }

    public function del(Request $request){
        $id = $request->input('id');
        $data = DB::table('address')->where('id',$id)->update(['del'=>0,'delete_at'=>time()]);
        return $this->apiSuccess('',$data);
    }
}
