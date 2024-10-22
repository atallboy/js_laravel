<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/9/26 18:02
 * @Description: 版权所有
 */

namespace Modules\Common\Models;

class Sms
{

    public function sendGySms($appcode,$mobile,$param,$smsSignId,$templateId){

        $host = "https://gyytz.market.alicloudapi.com";
        $path = "/sms/smsSend";
        $method = "POST";
//        $appcode = "ca7da8737286467d9ff4122b7fab9e75";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);

//smsSignId（短信前缀）和templateId（短信模板），可登录国阳云控制台自助申请。参考文档：http://help.guoyangyun.com/Problem/Qm.html

        $querys = "mobile=$mobile&param=**code**%3A12345%2C**minute**%3A5&smsSignId=2e65b1bb3d054466b82f0c9d125465e2&templateId=908e94ccf08b4476ba6c876d13f084ad";
        $querys = "mobile=$mobile&param=$param&smsSignId=$smsSignId&templateId=$templateId";
        $bodys = "";
        $url = $host . $path . "?" . $querys;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);  //如果只想获取返回参数，可设置为false
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $res = json_decode(curl_exec($curl),true);
//        echo "<pre>";print_r($res);echo "<pre>";
//        {"msg":"成功","smsid":"172735764310619322437179848","code":"0","balance":"30"}
        return $res;
    }



}
