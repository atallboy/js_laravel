<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/24 11:33
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuggestController extends ApiController
{
    public function index(Request $request){
        $list = DB::table('suggest')
            ->where('user_id',$this->user_id)
            ->where('del',1)
            ->orderBy('id','desc');
        $status = $request->input('status');
        if($status!=''){
            $list->where('status',$status);
        }
        $list = $list->get();
        foreach ($list as $k=>$v){
            $list[$k]->create_at = date('Y-m-d H:i:s',$v->create_at);
            $list[$k]->update_at = date('Y-m-d H:i:s',$v->update_at);
            $list[$k]->picArr = explode('&',$v->pic);

        }
        return $this->apiSuccess('',$list);
    }

    public function submit(Request $request){
        $inc = [
            'content'=>$request->input('content'),
            'pic'=>$request->input('pic'),
        ];
        $_id = DB::table('suggest')->insertGetId($this->cInc($inc));
        return $this->apiSuccess('');
    }
}
