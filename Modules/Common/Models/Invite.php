<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/8/18 18:42
 * @Description: 版权所有
 */

namespace Modules\Common\Models;

use Illuminate\Support\Facades\DB;

class Invite
{
    public function invitePrize($user_id){
        $user = DB::table('user')->where('id',$user_id)->where('del',1)->first();
        if($user->pre_id){
            $setting = DB::table('setting')->where('uniacid',$user->uniacid)->where('del',1)->first();
            $coupon_inc = [
                'cate'=>2,
                'uniacid'=>$user->uniacid,
                'user_id'=>$user->pre_id,
                'coupon_id'=>$setting->distribute_coupon,
                'remark'=>$user_id,
                'create_at'=>time()
            ];
            DB::table('coupon_record')->insert($coupon_inc);
        }
    }
}
