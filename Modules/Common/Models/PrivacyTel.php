<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/9/19 13:51
 * @Description: 版权所有
 */

namespace Modules\Common\Models;

use Illuminate\Support\Facades\DB;

class PrivacyTel
{

    private $baseUrl = 'https://101.37.133.245:11008'; // 替换为实际接口URL
    private $apId = '';
    private $token = '';
    private $middleNumber = '';
    private $notifyNumber = '';
    private $notifyTemplateId = '';


    function telDo($action,$param,$uniacid)
    {

        $setting =   DB::table('setting')->where('uniacid',$uniacid)->where('del',1)->first();
        $this->apId = $setting->privacy_tel_appid;
        $this->token = $setting->privacy_tel_token;
        $this->middleNumber = $setting->privacy_tel_number;
        $this->notifyNumber = $setting->privacy_tel_notify_number;
        $this->notifyTemplateId = $setting->privacy_tel_notify_template_id;


        if($action=='axb'){
            $action = 'middleNumberAXB';
            $param['middleNumber'] = $this->middleNumber;
        }
        if($action=='notify'){
            $param['templateId'] = $this->notifyTemplateId;
        }

        $appId = $this->apId;
        $token = $this->token;
        $time = explode (" ", microtime () );
        $timestamp = $time[1] . "000";

        $sig = md5($appId . $token . $timestamp);
        $url = $this->baseUrl . "/voice/1.0.0/$action/$appId/$sig";

        $authorization = base64_encode($appId . ":" . $timestamp);
        $header = array("Accept:application/json","Content-Type:application/json;charset=utf-8","Authorization:$authorization");
        $res = json_decode($this->curl_post($url,$param,$header),true);
        $data = array_merge($param,$res);
        return $data;
    }


    function curl_post($url,$data,$header,$post=1)
    {

        $data = json_encode($data);

        //初始化curl
        $ch = curl_init();
        //参数设置
        $res= curl_setopt ($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, $post);
        if($post)
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        $result = curl_exec ($ch);
        //连接失败
        if($result == FALSE){
            $result = "{\"result\":\"172001\",\"message\":\"网络错误\"}";
        }
        curl_close($ch);


        return $result;
    }
}


