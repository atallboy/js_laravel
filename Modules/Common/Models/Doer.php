<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/6/17 13:14
 * @Description: 版权所有
 */

namespace Modules\Common\Models;

class Doer
{
    public function dealData($data,$field=true,$time_format='Y-m-d H:i:s',$pic_host=''){

        $datatype = gettype($data);
        $wei = 1;
        $time_field = ['create_at','update_at','delete_at','recent_at','start_time','end_time'];
//        $time_format = 'Y-m-d H:i:s';
        $pic_field = ['pic','icon','src','field'];
//        if(!$pic_host)$pic_host = 'http://'.$_SERVER['HTTP_HOST'];
        if($datatype=='object'){
            foreach ($data as $k=>$v){
                if($k==0){$wei=2;break;}
            }
            if($wei==1){
                foreach ($data as $k=>$v){
                    if($v==null)$data[$k]='';
                }
                foreach ($time_field as $kt=>$vt){
                    if(property_exists($data,$vt)){$data->$vt = date($time_format,$data->$vt);}
                }
            }
            else if($wei==2){
                foreach ($data as $k=>$v){
                    foreach ($v as $key=>$val){
                        if($val===null)$data[$k]->$key='';
                    }
                    foreach ($time_field as $kt=>$vt){
                        if(property_exists($v,$vt)){
//                            echo "<pre>";print_r($v->$vt);echo "<pre>";
                            if($v->$vt)$data[$k]->$vt =  date($time_format,$v->$vt);
                        }
                    }
                    foreach ($pic_field as $kt=>$vt){
                        if(property_exists($v,$vt)){
                            if(!strpos($v->$vt,'ttp:'))$data[$k]->$vt = $pic_host.$v->$vt;
                        }
                    }
                }
            }
        }
        else{

        }
        return $data;
    }

    function distance($lat1, $lon1, $lat2,$lon2,$radius = 6378.137)
    {
        $rad = floatval(M_PI / 180.0);

        $lat1 = floatval($lat1) * $rad;
        $lon1 = floatval($lon1) * $rad;
        $lat2 = floatval($lat2) * $rad;
        $lon2 = floatval($lon2) * $rad;

        $theta = $lon2 - $lon1;

        $dist = acos(sin($lat1) * sin($lat2) +
            cos($lat1) * cos($lat2) * cos($theta)
        );

        if ($dist < 0 ) {
            $dist += M_PI;
        }

        return $dist = round($dist * $radius,2);
    }

    public static function createToken($str=''){
        //32 个字符组成一组随机字符串
        $randChars = self::getRandChar(32);
        //用三组字符串进行md5加密
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        // salt 盐
        $salt = config('secure.token_salt');
        return md5($randChars.$timestamp.$salt.$str);
    }

    public static function getRandChar($length)
    {
        $str    = null;
        $strPol = "0123456789abcdefghijklmnopqrstuvwxyz";
        $max    = strlen($strPol) - 1;

        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[rand(0, $max)];
        }
        return $str;
    }

    function generateRandomString($length = 6) {
        // 定义可用字符集，包括数字和大写字母
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        // 随机选择字符
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    function curl_get($url, &$httpCode = 0)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //不做证书校验,部署在linux环境下请改为true
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $file_contents = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $file_contents;
    }


    /**
     * 发送post请求
     * @param string $url
     * @param string $param
     * @return bool|mixed
     */
    function curl_post($url = '', $param = '',$cookie='')
    {
        if (empty($url) || empty($param)) {
            return false;
        }
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $postUrl); //抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        curl_setopt($ch, CURLOPT_COOKIE , $cookie);
        $data = curl_exec($ch); //运行curl
        curl_close($ch);
        return $data;
    }

    public function xmlToArray($xml) {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring), true);
        return $val;
    }

    public function isJson($string) {
        // 尝试解码字符串
        json_decode($string);
        // 使用 json_last_error() 检查是否发生了错误
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
