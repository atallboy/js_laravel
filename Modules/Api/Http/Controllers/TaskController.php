<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/8/21 14:02
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\log\LogServices;
use Modules\Api\Services\TaskServices;
use Modules\Common\Models\Pay;

class TaskController extends ApiController
{
    public function autoRun(Request $request){

        //先查看上次自动执行时间
        $q = DB::table('log')->where('uniacid',$this->uniacid)->where('cate',55)->orderBy('id','desc')->first();
        if($q&&$q->create_at>(time()-300))return;
//        var_dump($q);die;

        $pay = new Pay();
        $pay->uniacid = $this->uniacid;
        $pay->queryPaylogByCircle();

        $log = new LogServices();
        $log->uniacid = $this->uniacid;
        $log->user_id = -1;
        $log->event = 'autoRun';
        $log->remark = '';
        $log->content = '系统自动执行';
        $log->saveLog();

        $task = new TaskServices();
        $task->uniacid = $this->uniacid;
        $task->auto_receipt = $this->getsetting()->auto_receipt;
        $task->receiptOrder();


    }
}
