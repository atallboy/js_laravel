<?php

namespace Modules\Index\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{

    public function index(Request $request)
    {

        $p = $request->input();
        $uniacid = $request->input('i','');
        if(!$uniacid)die;
        $request->session()->put('uniacid', $uniacid);
        $share_token = $request->input('share_token');
        $act = $request->input('act');
        $redirect_url = $request->input('redirect_url');
        $request->session()->put('share_token', $share_token);
        $request->session()->put('redirect_url', $redirect_url);
        $request->session()->put('act', $act);
//        echo "<pre>";print_r($p);echo "<pre>";die;
//        $request->session()->put('home_token', '');
//        $request->session()->forget('home_token');
//        $value = '';
        $value = $request->session()->get('home_token');

        $user = DB::table('user')
            ->where('token',$value)
            ->where('uniacid',$uniacid)
            ->where('del',1)
            ->first();

//        echo "<pre>";print_r($value);echo "<pre>";
//        echo "<pre>";print_r($user);echo "<pre>";die;
        if(!$value||!$user || strlen($share_token)==6){
//            die;
            return redirect('/wechat/gzhLogin?i='.$uniacid);
        }
        else{


            $url = '/h5/index.html';
            if($request->session()->get('redirect_url')){
                $url = $request->session()->get('redirect_url');
            }
            $request->session()->put('share_token', '');
            $request->session()->put('redirect_url', '');
            $request->session()->put('act', '');
//            $request->session()->put('home_token', '');
            $request->session()->forget('share_token');
            $request->session()->forget('redirect_url');
            $request->session()->forget('act');
//            $request->session()->forget('home_token');
            return redirect($url);
//            echo "<pre>";print_r($value);echo "<pre>";
//            echo "<pre>";print_r($url);echo "<pre>";die;
        }
        echo $value;
    }


    public function download(Request $request)
    {
        if($this->isWechat()){
            return view('index::wechat');
        }else{
            return view('index::download');
        }
    }


    function isWechat(){
        $useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
        if(strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false ){
            return false;
        }else{
            preg_match("/.*?(MicroMessenger\/([0-9.]+))\s*/",$useragent, $matches);
            $version =  "你的微信版本号为:".$matches[2];
            return true;
        }
    }


    }
