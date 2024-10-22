<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/17 17:22
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Common\Models\Doer;

class ItemController extends ApiController
{
    public function detail(Request $request){
        $id = $request->input('id');
        $data = DB::table('item')->where('id',$id)->first();
        return $this->apiSuccess('',$data);
    }

    public function index(Request $request)
    {
        $master_select = $request->input('master_select');
        $master_id = $request->input('master_id');
        $price = $request->input('price');
        $condition = $request->input('condition');
        if($master_id){
            $list_query = DB::table('master_service')
                ->join('item','master_service.item_id','=','item.id')
                ->select(['item.*'])
                ->where('item.uniacid',$this->uniacid)
                ->where('item.del',1)
                ->where('master_service.master_id',$master_id)
                ->where('master_service.del',1)
                ->where('item.status',1);
            if($condition=='upgrade'){
                $list_query->where('item.price','>',$price);
            }
            $list = $list_query->get();
        }
        else{
            $list_query = DB::table('item')
                ->where('uniacid',$this->uniacid)
                ->where('del',1)
                ->where('status',1);
            if($condition=='upgrade'){
                $list_query->where('item.price','>',$price);
            }
            $list = $list_query->get();
        }


        foreach ($list as $k=>$v){
            if($master_select){
                $q = DB::table('master_service')->where('master_id',$master_select)->where('item_id',$v->id)->where('del',1)->first();
                $q?$list[$k]->master_select=true:$list[$k]->master_select=false;
            }
        }

        $list = (new Doer())->dealData($list);
        return $this->apiSuccess('',$list);
    }
}
