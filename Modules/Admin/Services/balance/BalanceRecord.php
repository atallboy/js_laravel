<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/22 09:18
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\balance;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\BaseService;

class BalanceRecord extends BaseService
{
    static function balanceChange($user_id,$money,$cate,$type=1,$_id='',$remark=''){

        $user = DB::table('user')->where('id',$user_id)->first();
        if(!$user)return ['code'=>0];
        $balance = $user->balance;

        $type==1?$after_balance=$balance+$money:$after_balance=$balance-$money;
        if($after_balance<0)return ['code'=>1,'msg'=>'余额不足'];

        $inc = [
            'uniacid'=>$user->uniacid,
            'user_id'=>$user_id,
            'type'=>$type,
            'cate'=>$cate,
            '_id'=>$_id,
            'money'=>$money,
            'before_balance'=>$balance,
            'after_balance'=>$after_balance,
            'remark'=>$remark,
            'create_at'=>time()
        ];
//        echo "<pre>";print_r($inc);echo "<pre>";die;
        $rid = DB::table('balance_record')->insertGetId($inc);

        DB::table('user')->where('id',$user_id)->update(['balance'=>$after_balance,'update_at'=>time()]);

        if($type==1){
            DB::table('user')->where('id',$user_id)->increment('total_balance',$money);
        }
        if($type==2){
            DB::table('user')->where('id',$user_id)->decrement('total_balance',$money);
        }

        return ['code'=>1];
    }

    static function checkBalanceTypeDesc($cate,$rid=0){

        $msg = [
            1=>['describe'=>'充值'],
            2=>['describe'=>'后台充值'],
            3=>['describe'=>'技师服务费到账'],
            4=>['describe'=>'区域代理佣金'],
            5=>['describe'=>'出行费'],
            6=>['describe'=>'加钟费'],
            7=>['describe'=>'常规业绩定期结算'],
            8=>['describe'=>'加钟业绩定期结算'],

            11=>['describe'=>'提现退回'],
            12=>['describe'=>'订单退款'],
            20=>['describe'=>'订单余额支付'],
            21=>['describe'=>'余额提现'],

            31=>['describe'=>'一级分销佣金'],
            32=>['describe'=>'二级分销佣金'],
            33=>['describe'=>'三级分销佣金'],
        ];

        $remark = '';

        $r = [
            'describe'=>$msg[$cate]['describe'],
            'remark'=>$remark
        ];

        return $r;
    }

}
