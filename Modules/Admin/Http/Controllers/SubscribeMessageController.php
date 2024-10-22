<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/7/30 08:19
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\subscribe\SubscribeService;
use Modules\Common\Models\SubscribeMessage;

class SubscribeMessageController extends BaseController
{

    public function index(Request $request){

        return (new SubscribeService())->index($request->only(['page','limit','status','name']));
    }

    function edit(Request $request)
    {
        return (new SubscribeService())->edit($request->only(['status','id','name','trigger_event','template_id','form']));
    }

    public function del(Request $request){
        return (new SubscribeService())->del($request->input('id'));
    }

    public function getSubscribeEvent(){

        $arr = [
            ['id'=>1,'name'=>'新订单通知师傅']
        ];

        return $this->apiSuccess('',$arr);
    }


    public function getSubscribeParam(){

        $data = (new SubscribeMessage())->getSubscribeData();

        return $this->apiSuccess('',$data);
    }

    public function sendSubscribeTest(Request $request){
        $data = $request->only(['openid','id']);
        $subscribe = DB::table('subscribe_message')->where('id',$data['id'])->first();
        $sub = new SubscribeMessage();
        $param = $sub->getSubscribeDataValue($subscribe,$data['openid']);
        $sub->uniacid = 1;
        $res = $sub->sendSubscribeMessage($param);
        return $this->apiSuccess('',$res);
    }


}
