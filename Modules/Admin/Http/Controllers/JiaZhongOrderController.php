<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/19 15:12
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\order\OrderService;
use Modules\Common\Models\Pay;

class JiaZhongOrderController extends BaseController
{
//获得加钟订单
public function  index(Request $request)
{

    $perPage = request('per_page', 10);

// 假设 $status 是状态参数，从请求中获取，如果没有提供则不添加此条件
    $status = request('status', null);

    $jiazhong_record = DB::table('jiazhong_record')
        ->join('item', 'jiazhong_record.item_id', '=', 'item.id')
        ->select(['jiazhong_record.*', 'item.name']);

    if($status!=null)$jiazhong_record->where('jiazhong_record.status',$status);

    $list = $jiazhong_record->paginate($perPage);
    return $this->apiSuccess('',$list);


}

//删除加钟订单

public function del(Request $request)
{

    $id = $request->input('id'); //要删除的加钟订单id


    $res = Db::table('jiazhong_record')->where('id',$id)->delete();

    if($res){

        return ['code'=>20000,'message'=>'删除加钟订单成功'];

    }else{

        return  ['code'=>400,'message'=>'删除加钟订单失败'];
    }


}



//结束符
}
