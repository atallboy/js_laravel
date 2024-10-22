<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/9/30 17:35
 * @Description: 版权所有
 */

namespace Modules\Api\Services;

use Illuminate\Support\Facades\DB;

class UserService
{

    public function getUserDistributeMchInfo($user_id){
        $data = DB::table('distribute_mch')->where('user_id',$user_id)->where('del',1)->first();
        return $data;
    }

    public function getUserDistributeQrcode($user_id){
        $data = DB::table('distribute_mch')->where('user_id',$user_id)->where('del',1)->first();
        if($data){
            $qrcode = DB::table('distribute_qrcode')->where('distribute_mch_id',$data->id)->where('del',1)->first();
            if($qrcode){
                return $qrcode;
            }
        }
        return false;
    }

}
