<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/8/26 07:54
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\distribute\DistributeMchService as service;

class DistributeMchController extends BaseController
{
    function index(Request $request)
    {
        return (new service())->index($request->only(['page','limit','status','name','role']));
    }

    function approve(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');
        $res = DB::table('distribute_mch')->where('id',$id)->update(['status'=>$status,'update_at'=>time()]);
        if($status==1){
            $m = DB::table('distribute_mch')->where('id',$id)->first();
            $q = DB::table('distribute_mch_performance')->where('distribute_mch_id',$id)->first();
            if(!$q){
                DB::table('distribute_mch_performance')->insert(['distribute_mch_id'=>$id,'user_id'=>$m->user_id,'uniacid'=>$this->uniacid,'create_at'=>time()]);
            }
            DB::table('distribute_qrcode')->where('distribute_mch_id',$id)->where('status',0)->update(['status'=>1,'update_at'=>time()]);
        }else{
            DB::table('distribute_qrcode')->where('distribute_mch_id',$id)->where('status',0)->update(['distribute_mch_id'=>0,'update_at'=>time()]);
        }
        return $this->apiSuccess('操作成功',$res);
    }

    function edit(Request $request)
    {
        return (new service())->edit($request->only([
            'status','id','name','tel',
            'open_percent','percent_first','percent_second','percent_third',
        ]));
    }


    public function del(Request $request){
        return (new service())->del($request->input('id'));
    }
}
