<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/10/17 13:26
 * @Description: 版权所有
 */

namespace Modules\Api\Services;

use Illuminate\Support\Facades\DB;

class MasterService
{
    public function checkMasterTimeFree($id,$time){
        $time_status = DB::table('order')
            ->where('master_id',$id)
            ->where('service_time',$time)
            ->where('status','>',0)
            ->where('status','!=',5)
            ->where('status','!=',12)
            ->where('del',1)
            ->first();
        return $time_status?false:true;
    }
}
