<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/6/12 11:27
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Modules\Admin\Services\log\LogServices;
use Modules\Admin\Services\user\UserService;
use Modules\Common\Models\SubscribeMessage;
use Modules\Common\Models\Wechat;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UserController extends ApiController
{

    function get(Request $request)
    {

//        $subscribe = new SubscribeMessage();
//        $subscribe->user_id = 8;
//        $subscribe->order_id = 8;
//        $subscribe->sendMessage();die;

        $user = DB::table('user')
            ->where('id',$this->user_id)
            ->where('del',1)
            ->first();
        if(!$user){
            $request->session()->put('home_token', '');
            $request->session()->forget('home_token');
            return $this->apiError('登录失效，需要重新登录');
        }
        else{
            if(!$user->nickName){
                $user->avatarUrl = $this->host.'/static/img/11.jpg';
                $user->nickName = '用户_'.($user->id+2)*35;
            }
//            if($user->token=='dc39be313848b81982af956070a06716'){
//                $user->nickName = '体验用户';
//                $user->avatarUrl = 'https://jsdjys.jiuzhouzhichuang.net/static/img/11.jpg';
//            }

            $user->master = $this->master;
            $user->agent = $this->agent;
            $user->distribute_mch_id = $this->distribute_mch_id;
        }

//        if(!$user->qrcode_gzh){
//            (new UserService())->createQrcode($user->id,$this->host);
//        }
        $qrcode_gzh = DB::table('user')
                ->where('id',$this->user_id)
                ->where('del',1)
                ->value('qrcode_gzh');
        if($qrcode_gzh)$user->qrcode_gzh = $this->host.$qrcode_gzh.'?r='.rand(1000,99999);


//        echo "<pre>";print_r($this->user_id);echo "<pre>";die;
//        $rr = 'https://ditu.amap.com/dir?type=car&from%5Blnglat%5D=-95.712891%2C37.09024&from%5Bname%5D=%E6%88%91%E7%9A%84%E4%BD%8D%E7%BD%AE&to%5Blnglat%5D=113.848371%2C31.590361&to%5Bname%5D=%E7%9B%AE%E7%9A%84%E5%9C%B0&src=uriapi&innersrc=uriapi&policy=1';
//        $user->c = urldecode($rr);
//
//
//           $p = [
//               'type'=>'car',
//               'src'=>'uriapi',
//               'innersrc'=>'uriapi',
//               'policy'=>1,
////               'from'=>['lnglat'=>'-95.712891,37.09024','name'=>'好多呃'],
//               'to'=>['lnglat'=>'113.848371,31.590361','name'=>'哈哈哈123'],
//           ];
//
//        $query_string = http_build_query($p, '', '&', PHP_QUERY_RFC3986);
//        $url = "https://ditu.amap.com/dir?" . $query_string;
//        $user->c2 = ($url);

        return $this->apiSuccess('',$user);
    }






}
