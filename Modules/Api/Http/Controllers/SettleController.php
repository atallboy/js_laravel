<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/8/3 21:56
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\settle\SettleService;
use Modules\Common\Models\Doer;

class SettleController extends ApiController
{
    public function index(){
        $settle = new SettleService();
        $settle->initSettle();

        $settle_time_list = DB::table('settle_time')
//            ->where('create_at','<',$this->master->create_at)
//            ->where('uniacid',$this->uniacid)
            ->where('del',1)
            ->get();
        foreach ($settle_time_list as $k=>$v){
            $q = DB::table('settle_record')
                ->where('user_id',$this->user_id)
                ->where('settle_time_id',$v->id)
                ->where('del',1)
                ->first();
            if(!$q){
                $inc = [
                    'uniacid'=>$this->uniacid,
                    'user_id'=>$this->user_id,
                    'settle_time_id'=>$v->id,
                    'order_num'=>0,
                    'achieve_money'=>0,
                    'reward_money'=>0,
                    'status'=>0,
                    'create_at'=>time()
                ];
                $settle_record_id = DB::table('settle_record')->insertGetId($inc);
            }
        }

        $list = DB::table('settle_record')
            ->join('settle_time','settle_record.settle_time_id','=','settle_time.id')
            ->join('settle_solution','settle_time.settle_solution_id','=','settle_solution.id')
            ->where('settle_record.user_id',$this->user_id)
            ->orderBy('settle_record.id','desc')
            ->select(['settle_record.*','settle_time.start_time','settle_time.end_time','settle_solution.name'])
            ->get();

//        echo "<pre>";print_r($settle_time_list);echo "<pre>";
        return $this->apiSuccess('',(new Doer())->dealData($list));
    }

    public function settle(Request $request){
        return $this->apiSuccess('',(new SettleService())->settle($this->master,$request->input('id')));
    }

}
