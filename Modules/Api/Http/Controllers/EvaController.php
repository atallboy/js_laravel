<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/25 16:38
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\log\LogServices;
use Modules\Admin\Services\master\MaterService;
use Modules\Api\Services\OrderServices;

class EvaController extends ApiController
{
    public function index(Request $request)
    {
        $list = DB::table('eva_record')
            ->where('cate',$request->input('cate'))
            ->where('master_id',$request->input('master_id'))
            ->where('del',1)
            ->orderBy('id','desc')
            ->get();

        foreach ($list as $k=>$v){
            $list[$k]->create_at = date('Y-m-d',$v->create_at);
            $list[$k]->tagArr = explode('&',$v->tag);
        }

        return $this->apiSuccess('',$list);
    }

    public function submit(Request $request){

        $cate = $request->input('cate');
        $order_id = $request->input('order_id');
        $score = $request->input('score');

        $order = DB::table('order')->where('id',$order_id)->where('user_id',$this->user_id)->first();
        if(!$order)return $this->apiError();
        $pro = DB::table('order_product')->where('order_id',$order_id)->where('del',1)->first();
        $inc = [
            'cate'=>$cate,
            'order_id'=>$order->id,
            'master_id'=>$order->master_id,
            'item_id'=>$pro->item_id,
            'score'=>$score,
            'content'=>$request->input('content'),
            'tag'=>$request->input('tag'),
        ];
        $c = $this->cInc($inc);
//        return $this->apiSuccess('',$c);
        $id = DB::table('eva_record')->insertGetId($c);

        $master_performance_id = (new MaterService())->getMasterPerformanceIdByMasterId($order->master_id);
        $master_performance = DB::table('master_performance')->where('id',$master_performance_id)->where('del',1)->first();
        if(!$master_performance)return $this->apiError('数据缺失',['$master_performance_id'=>$master_performance_id,'$order'=>$order]);
        $master_score = round((($master_performance->eva_order*$master_performance->score+$score)/($master_performance->eva_order+1)),1);
        $ud = [
            'eva_order'=>$master_performance->eva_order+1,
            'score'=>$master_score,
            'update_at'=>time()
        ];
        DB::table('master_performance')->where('id',$master_performance->id)
            ->update($ud);

        $log = new LogServices();
        $log->uniacid = $this->uniacid;
        $log->user_id = $this->user_id;
        $log->event = 'orderDo';
        $log->remark = $order_id;
        $log->content = '服务评价';
        $log->saveLog();

        DB::table('order')->where('id',$order_id)->update(['status'=>OrderServices::getOrderStatusCode('eva'),'update_at'=>time()]);

        return $this->apiSuccess('',[$ud,$c]);
    }
}
