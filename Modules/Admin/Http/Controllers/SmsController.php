<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/9/28 21:22
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\banner\BannerService;
use Modules\Admin\Services\log\LogServices;
use Modules\Api\Services\OrderServices;
use Modules\Common\Models\Sms;

class SmsController extends BaseController
{
    function index(Request $request)
    {
        $data = DB::table('sms_setting')->where('uniacid',$this->uniacid)->where('del',1)->first();
        if(!$data)DB::table('sms_setting')->insert(['uniacid'=>1,'create_at'=>time()]);
        $data = DB::table('sms_setting')->where('uniacid',$this->uniacid)->where('del',1)->first();

        return $this->apiSuccess('',$data);
    }

    function edit(Request $request)
    {

        $inc = [
            'order_notice'=>$request->input('order_notice'),
            'appcode'=>$request->input('appcode'),
            'smsSignId'=>$request->input('smsSignId'),
            'templateId'=>$request->input('templateId'),
            'template_notice_id'=>$request->input('template_notice_id'),
            'update_at'=>time()
        ];

        $res = DB::table('sms_setting')->where('uniacid',$this->uniacid)->where('del',1)->update($inc);
        return $this->apiSuccess('',$res);
    }


    function testSendSms(Request $request)
    {
        $cate = $request->input('cate');
        $data = DB::table('sms_setting')->where('uniacid',$this->uniacid)->where('del',1)->first();

        $phone_number = '13476143979';
        $phone_number = $request->input('phone');
        $appcode = $data->appcode;
        $smsSignId = $data->smsSignId;
        if($cate==1){
            $param = "**code**%3A1234%2C**minute**%3A5";
            $templateId = $data->templateId;
        }
        if($cate==2){
            $param = "您有新的茶约到家订单，请及时处理!";
            $param = "**order_no**%3A123456";
            $templateId = $data->template_notice_id;
        }



//        $querys = "mobile=$mobile&param=**code**%3A12345%2C**minute**%3A5&smsSignId=2e65b1bb3d054466b82f0c9d125465e2&templateId=908e94ccf08b4476ba6c876d13f084ad";

        $res = (new Sms())->sendGySms($appcode,$phone_number,$param,$smsSignId,$templateId);
        $res['phone'] = $phone_number;
        $res['param'] = $param;

        $log = new LogServices();
        $log->uniacid = $this->uniacid;
        $log->user_id = $this->admin_id;
        $log->event = 'testSendSms';
        $log->remark = $phone_number;
        $log->content = json_encode($res);
        $log->saveLog();

        return $this->apiSuccess('',$res);
    }

}
