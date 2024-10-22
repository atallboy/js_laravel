<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/8/21 14:04
 * @Description: 版权所有
 */

namespace Modules\Api\Services;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\log\LogServices;

class TaskServices
{
    public $uniacid;
    public $auto_receipt;
    public function receiptOrder(){
        $time = time()-3600*$this->auto_receipt;
        $list = DB::table('order')
            ->where('uniacid',$this->uniacid)
            ->where('start_service_time','<',$time)
            ->where('status',2)
            ->where('del',1)
            ->get();

        $log = new LogServices();
        $log->uniacid = $this->uniacid;
        $log->user_id = -1;
        $log->event = 'orderDo';

        foreach ($list as $k=>$v){
            $log->remark = $v->id;
            (new OrderServices())->doOrderFinish($v->id);
            $log->content = '订单完成（系统自动签收）';
            $log->saveLog();
        }

        return true;
    }
}
