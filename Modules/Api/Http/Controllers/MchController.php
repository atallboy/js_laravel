<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/8/21 22:36
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Common\Models\Doer;

class MchController extends ApiController
{
    public function index(Request $request)
    {
        $cate_id = $request->input('cate_id');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $list = DB::table('mch')
            ->where('uniacid',$this->uniacid)
            ->where('del',1)
            ->orderBy('id','desc');
        if(!$cate_id || $cate_id=='nearby'){

        }elseif ($cate_id=='recommend'){
            $list->where('is_recommend',1);
        }
        $list =  $list ->get();
        $doer = new Doer();
        $arr = [];
        foreach ($list as $k=>$v){
            $distance = $doer->distance($latitude,$longitude,$v->latitude,$v->longitude);
            $list[$k]->_distance = $distance;
            $list[$k]->distance = $distance.'km';
            foreach ($list[$k] as $k1=>$v1){
                $arr[$k][$k1] = $v1;
            }
        }
//        $list = $doer->dealData($list);

        array_multisort(array_column($arr, '_distance'), SORT_ASC, $arr);

        return $this->apiSuccess('',$arr);
    }

    public function detail(Request $request){
        $id = $request->input('id');
        $data = DB::table('mch')->where('id',$id)->first();
        return $this->apiSuccess('',$data);
    }
}
