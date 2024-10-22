<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/22 21:54
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Support\Facades\DB;

class BalanceRecord extends ApiController
{
    public function record(){
        $list = DB::table('')->where('user_id',$this->user_id)->where('del',1)
            ->orderBy('id','desc')
            ->get();
        
        return $this->apiSuccess($list);
    }
}
