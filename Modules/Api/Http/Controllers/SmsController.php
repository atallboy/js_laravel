<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/9/26 21:29
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\log\LogServices;
use Modules\Common\Models\Sms;

class SmsController extends ApiController
{
    public function send(Request $request){

        if(!$this->user_id)return $this->apiError('未登录或登录失效，请重新登录');
        $phone_number = $request->input('phone_number');

        $code = rand(1000,9999);
        $data = DB::table('sms_setting')->where('uniacid',$this->uniacid)->where('del',1)->first();
        $appcode = 'ca7da8737286467d9ff4122b7fab9e75';
        $smsSignId = '2e65b1bb3d054466b82f0c9d125465e2';
        $templateId = '908e94ccf08b4476ba6c876d13f084ad';

        $appcode = $data->appcode;
        $smsSignId = $data->smsSignId;
        $templateId = $data->templateId;

        $param = "**code**%3A$code%2C**minute**%3A5";

//        $querys = "mobile=$mobile&param=**code**%3A12345%2C**minute**%3A5&smsSignId=2e65b1bb3d054466b82f0c9d125465e2&templateId=908e94ccf08b4476ba6c876d13f084ad";

        $res = (new Sms())->sendGySms($appcode,$phone_number,$param,$smsSignId,$templateId);

        $log = new LogServices();
        $log->uniacid = $this->uniacid;
        $log->user_id = $this->user_id;
        $log->event = 'sendSmsCode';
        $log->remark = $phone_number;
        $res['param'] = $param;
        $log->content = json_encode($res);
        $log->saveLog();

        if(array_key_exists('code',$res) && $res['code']==0){
            $inc = [
                'uniacid'=>$this->uniacid,
                'code'=>$code,
                'phone_number'=>$phone_number,
                'status'=>0,
                'create_at'=>time()
            ];
            DB::table('sms_verification')->insert($inc);
        }
        else{
            return $this->apiError($res['msg']);
        }
//        echo "<pre>";print_r($res);echo "<pre>";

        return $this->apiSuccess('',$res);
    }

    public function verify(Request $request){

        $phone_number = $request->input('phone_number');
        $code = $request->input('code');

        $query = DB::table('sms_verification')
            ->where('phone_number',$phone_number)
            ->where('code',$code)
            ->where('uniacid',$this->uniacid)
            ->where('status',0)
            ->where('del',1)
            ->first();

        if(!$query)return $this->apiError('验证码错误');
        if($query->create_at < (time()-300))return $this->apiError('验证码过期，请重新获取');

        DB::table('sms_verification')->where('id',$query->id)->where('del',1)->update(['status'=>1,'update_at'=>time()]);
        DB::table('user')->where('id',$this->user_id)->where('del',1)
            ->update(['tel'=>$phone_number,'get_tel_time'=>time(),'update_at'=>time()]);

        return $this->apiSuccess('');
    }


}
