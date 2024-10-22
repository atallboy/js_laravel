<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Common\Models\RunLog;

class ApiController extends Controller
{
    public $agent_cate;
    public $host;
    public $static_host;
    public $uniacid;
    public $token;
    public $user_id;
    public $user;
    public $master_id;
    public $master;

    public $agent_id;
    public $agent;

    public $distribute_mch_id;
    public $distribute_mch;

    public $inc;
    public $inc_u;

    function __construct(Request $request)
    {

//        (new RunLog())->recordRunning($request);

//        $this->host = 'https://jishi.lincec.top';
        $this->host = 'https://'.$_SERVER['HTTP_HOST'];
        $this->static_host = $this->host.'/upload/';
        $i = $request->header('i');
        if(!$i)$i=$request->input('i');
        $this->uniacid = $i;
        $token = $request->header('token');
        $this->agent_cate = 'micro';
         if(!$token){
            $token = $request->session()->get('home_token');
            if(!$token){
        //            $token='28d454220722f21cd4422ce96954a962';
            }
            $this->agent_cate = 'gzh';
         }
//        $session = $request->session()->all();
//        echo "<pre>";print_r($i);echo "<pre>";die;

        if($token){
            $this->token = $token;
            $user = DB::table('user')
                ->where('uniacid',$this->uniacid)
                ->where('token',$token)
                ->where('del',1)
                ->first();
            if($user){
//                if($token=='dc39be313848b81982af956070a06716'){
//                    $user->nickName = '体验用户';
//                    $user->avatarUrl = 'https://jsdjys.jiuzhouzhichuang.net/static/img/11.jpg';
//                }
                DB::table('user')->where('id',$user->id)->update(['recent_at'=>time()]);
                $this->user_id = $user->id;
                $this->user = $user;
                $master = DB::table('master')->where('cate',1)->where('user_id',$user->id)->where('del',1)->where('status','<',2)->orderBy('id','desc')->first();
                if($master){
                    $this->master_id = $master->id;
                    $master->picArr = explode('&',$master->pic);
                    $master->idcardArr = explode('&',$master->idcard);
                    $master->certificateArr = $master->certificate?explode('&',$master->certificate):$master->certificate=[];
                    $this->master = $master;
                }
                $agent = DB::table('agent')->where('user_id',$this->user_id)->where('status','<',2)->where('del',1)->orderBy('id','desc')->first();
                if($agent){
                    $this->agent_id = $agent->id;
                    $this->agent = $agent;
                }

                $distribute_mch = DB::table('distribute_mch')->where('uniacid',$this->uniacid)->where('user_id',$this->user_id)->orderBy('id','desc')->first();
                if($distribute_mch && $distribute_mch->del==0)$distribute_mch=false;
                if($distribute_mch && $distribute_mch->status!=1)$distribute_mch=false;
                if($distribute_mch){
                    $this->distribute_mch_id = $distribute_mch->id;
                    $this->distribute_mch = $distribute_mch;
                }
            }
        }

        $this->inc = [
            'uniacid'=>$this->uniacid,
            'create_at'=>time(),
            'update_at'=>time()
        ];
        $this->inc_u['user_id'] = $this->user_id;

    }

    public $setting;
    public function getsetting(){
        $setting =   DB::table('setting')->where('uniacid',$this->uniacid)->where('del',1)->first();
        if($this->agent_cate=='gzh'){
            $setting->app_id = $setting->gzh_appid;
            $setting->app_secret = $setting->gzh_appsecret;
        }
        return $this->setting = $setting;
    }

    public function cInc($data,$fill_user=true){
        $data['uniacid'] = intval($this->uniacid);
        $data['create_at'] = time();
        $data['update_at'] = time();
        if($fill_user)$data['user_id'] = $this->user_id;
        return $data;
    }

    public function validateErrorMsg($validator){
        return array_values($validator->errors()->messages())[0][0];
    }

    public function apiSuccess(string $message = '操作成功',$data = array(),int $status = 20000){
        return response()->json([
            'status' => $status,
            'message'=> $message,
            'data'=>$data
        ],200);
    }

    public function apiError(string $message = '失败',$data = array(),int $status = 40000){
        return response()->json([
            'status' => $status,
            'message'=> $message,
            'data'=>$data
        ],400);
    }



}
