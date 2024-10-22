<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/9/20 13:36
 * @Description: 版权所有
 */
/** AXB模式绑定示例代码 */
function testAxbBind()
{

    global $PRIVACY_AXB_URL;
    $PRIVACY_AXB_URL = '/voice/1.0.0/middleNumberAXB';
    $axbBody = new PrivacyAxbBindBody();




    /**设置为平台分配的隐私号码*/
    $axbBody->middleNumber = "02028187120";
    /**设置为需要绑定的号码A*/
    $axbBody->bindNumberA = "13112345687";
    /**设置为需要绑定的号码B*/
    $axbBody->bindNumberB = "13912578521";
    /**是否需要录音，需要录音设置为1，不需要录音设置为0*/
    $axbBody->callRec = 1;
    /**设置绑定时间，单位秒，当设置为0时，则永久绑定*/
    $axbBody->maxBindingTime = 60;
    /**设置话单的推送地址*/
    $axbBody->callbackUrl="";


    /**设置为平台分配的account*/
    $account = "358484";
    /**设置为平台分配的token*/
    $token="5c77caca300a4723b51ff3f2ea89185f";

    $sender = new WinnerVoiceSender($account,$token,true);

    $result = $sender->sendRequest($PRIVACY_AXB_URL,json_encode($axbBody));

    $voice_result = json_decode($result);


    echo $result;
    if($voice_result->result == '000000')
    {
        echo "\n绑定成功";
    }
    else {
        echo "\n绑定失败";
    }
}


testAxbBind();

class WinnerVoiceSender
{

    private $account;
    private $token;
    private $isHttps;

    /**
     * PrivacyAxbBindBody constructor.
     * @param $accountId
     * @param $token
     * @param $url
     */
    public function __construct($accountId, $token,$isHttps)
    {
        $this->account = $accountId;
        $this->token = $token;
        $this->isHttps = $isHttps;
    }

    /**
     *
     *返回字符串的毫秒数时间戳
     */
    function get_total_millisecond()
    {
        $time = explode (" ", microtime () );
        $time = $time[1] . "000";
        return $time;
    }

    function getAuthorizationHeader($timeStamp)
    {
        $str = $this->account.":".$timeStamp;
        $header = base64_encode($str);
        return $header;
    }

    function getSig($timeStamp)
    {
        return md5("$this->account$this->token$timeStamp");
    }

    function sendRequest($voice_url,$body)
    {
        global $HTTP_VOICE_SERVER,$HTTPS_VOICE_SERVER;
        $HTTP_VOICE_SERVER = 'https://101.37.133.245:11008';
        $HTTPS_VOICE_SERVER = 'https://101.37.133.245:11008';
        if($this->isHttps)
        {
            $url=$HTTPS_VOICE_SERVER . $voice_url;
        }
        else {
            $url=$HTTP_VOICE_SERVER . $voice_url;
        }

        $timeStamp = $this->get_total_millisecond();
//        $timeStamp = 1726811022029;

        $sig = $this->getSig($timeStamp);


        $post_url = "$url/$this->account/$sig";

        $auth_header = $this->getAuthorizationHeader($timeStamp);
        $header = array("Accept:application/json","Content-Type:application/json;charset=utf-8","Authorization:$auth_header");
        echo "<pre>";print_r($timeStamp);echo "<pre>";
        echo "<pre>";print_r($post_url);echo "<pre>";
        echo "<pre>";print_r($body);echo "<pre>";
        echo "<pre>";print_r($header);echo "<pre>";
        $result = $this->curl_post($post_url,$body,$header);

        return $result;
    }

    /**
     * 发起HTTPS请求
     * @param $url
     * @param $data
     * @param $header
     * @param int $post
     * @return bool|string
     */
    function curl_post($url,$data,$header,$post=1)
    {
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



class PrivacyAxbBindBody
{
    public $middleNumber;
    public $bindNumberA;
    public $bindNumberB;
    public $callRec;
    public $maxBindingTime;
    public $callbackUrl;
    public $passthroughCallerToA;
    public $passthroughCallerToB;

    /**
     * PrivacyAxbBindBody constructor.
     */
    public function __construct()
    {
    }


}
