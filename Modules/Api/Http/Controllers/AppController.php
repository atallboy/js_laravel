<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/9/10 16:20
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\log\LogServices;
use Modules\Common\Models\Doer;
use Modules\Common\Models\Invite;
use Modules\Common\Models\Wechat;

class AppController extends ApiController
{
    public function appWechatLogin(Request $request)
    {
        $code = $request->input('code');

        // 通过 code 获取 session_key 和 openid
        $wechatData = (new Wechat())->getAppWechatAccessToken($code,$this->uniacid);
        $log = new LogServices();
        $log->uniacid = $this->uniacid;
        $log->user_id = -1;
        $log->event = 'autoRun';
        $log->remark = $code;
        $log->content = json_encode($wechatData);
        $log->saveLog();

        $wechatUserData = (new Wechat())->getAppWechatUserInfo($wechatData['access_token'],$wechatData['openid']);
        $log = new LogServices();
        $log->uniacid = $this->uniacid;
        $log->user_id = -1;
        $log->event = 'autoRun';
        $log->remark = $code;
        $log->content = json_encode($wechatUserData);
        $log->saveLog();

        $userInfo = [
            'nickName'=>$wechatUserData['nickname'],
            'avatarUrl'=>$wechatUserData['headimgurl'],
            'province'=>$wechatUserData['province'],
            'city'=>$wechatUserData['city'],
            'gender'=>$wechatUserData['sex'],
            'update_at'=>time(),
        ];

        $user = DB::table('user')
            ->where('uniacid',$this->uniacid)
            ->where('openid',$wechatData['openid'])
            ->where('del',1)
            ->first();

        if(!$user){
            $token = Doer::createToken();
            $userInfo['uniacid'] = $this->uniacid;
            $userInfo['token'] = $token;
            $userInfo['channel'] = 2;
            $userInfo['openid'] = $wechatData['openid'];
            $userInfo['session_key'] = '';
            $userInfo['withdrawal'] = 0;
            $userInfo['create_at'] = time();
            if(array_key_exists('unionid',$wechatUserData))$userInfo['unionid'] = $wechatUserData['unionid'];

            $id = DB::table('user')->insertGetId($userInfo);

        }else{
            if(array_key_exists('unionid',$wechatUserData)&&!$user->unionid)$userInfo['unionid'] = $wechatUserData['unionid'];
            DB::table('user')->where('id',$user->id)->update($userInfo);
            $token = $user->token;
        }

        return $this->apiSuccess('',$token);


        echo "<pre>";print_r($code);echo "<pre>";

    }
}
