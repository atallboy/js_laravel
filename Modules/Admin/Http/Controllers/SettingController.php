<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/6/12 18:14
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\admin\AdminService;
use Modules\Common\Models\Doer;

class SettingController extends BaseController
{
    function index()
    {

        $data = DB::table('setting')->where('uniacid',$this->uniacid)->first();
        if(!$data)DB::table('setting')->insert(['uniacid'=>1,'gzh_appid'=>'','create_at'=>time()]);
        $data = DB::table('setting')->where('uniacid',$this->uniacid)->first();
        $data->mch_cert_pem = '';
        $data->mch_key_pem = '';
        $data->m_link = $_SERVER['HTTP_HOST'].'/home/index?i='.$data->uniacid;
        $data->refund_destination = strval($data->refund_destination);

        return response(['code'=>20000,'data'=>$data]);
    }

    public function edit(Request $request){

        $inc = [
            'name'=>$request->input('name'),
            'tel'=>$request->input('tel'),
            'desc'=>$request->input('desc'),
            'app_id'=>$request->input('app_id'),
            'app_secret'=>$request->input('app_secret'),
            'refund_destination'=>$request->input('refund_destination'),
            'gzh_appid'=>$request->input('gzh_appid'),
            'gzh_appsecret'=>$request->input('gzh_appsecret'),
            'mch_id'=>$request->input('mch_id'),
            'mch_secret'=>$request->input('mch_secret'),
            'master_percent'=>$request->input('master_percent'),
            'agent_percent'=>$request->input('agent_percent'),
            'distribute_coupon'=>$request->input('distribute_coupon'),
            'eva_tag'=>$request->input('eva_tag'),
            'open_service_link'=>$request->input('open_service_link'),
            'service_link'=>$request->input('service_link'),
            'city_arr'=>$request->input('city_arr'),
            'auto_receipt'=>$request->input('auto_receipt'),
            'register_top_pic'=>$request->input('register_top_pic'),
            'master_rest_text'=>$request->input('master_rest_text'),
            'travel_fee_text'=>$request->input('travel_fee_text'),
            'privacy_tel_status'=>$request->input('privacy_tel_status'),
            'privacy_tel_notify_status'=>$request->input('privacy_tel_notify_status'),
            'privacy_tel_appid'=>$request->input('privacy_tel_appid'),
            'privacy_tel_token'=>$request->input('privacy_tel_token'),
            'privacy_tel_number'=>$request->input('privacy_tel_number'),
            'privacy_tel_notify_number'=>$request->input('privacy_tel_notify_number'),
            'privacy_tel_notify_template_id'=>$request->input('privacy_tel_notify_template_id'),
            'open_auth_phone'=>$request->input('open_auth_phone'),
            'update_at'=>time()
        ];

        $mch_cert_pem = $request->input('mch_cert_pem');
        if($mch_cert_pem){
            $mch_cert_pem_name = $this->uniacid.'_cert_'.md5(time().'_cert');
            $inc['mch_cert_pem'] = $mch_cert_pem_name;
            $path = storage_path("app/cert/".$mch_cert_pem_name.".pem");
            $file_mch_cert_pem = fopen($path, "w");
            fwrite($file_mch_cert_pem, $mch_cert_pem);
            fclose($file_mch_cert_pem);
        }
        $mch_key_pem = $request->input('mch_key_pem');
        if($mch_key_pem){
            $mch_key_pem_name = $this->uniacid.'_cert_'.md5(time().'_key');
            $inc['mch_key_pem'] = $mch_key_pem_name;
            $path = storage_path("app/cert/".$mch_key_pem_name.".pem");
            $file_mch_key_pem = fopen($path, "w");
            fwrite($file_mch_key_pem, $mch_key_pem);
            fclose($file_mch_key_pem);
        }

        $res = DB::table('setting')->where('uniacid',$this->uniacid)->update($inc);

        //先找当前登录用户
        $account = $request->input('account');
        $password = $request->input('password');
        $account2 = $request->input('account2');
        $password2 = $request->input('password2');
        if($account2||$password2){

            if(!$account)return response(['code'=>40000,'message'=>'请输入当前登录账号','data'=>'']);
            if(!$password)return response(['code'=>40000,'message'=>'请输入当前登录密码','data'=>'']);
            if(!$this->admin_id)return response(['code'=>40000,'message'=>'未登录','data'=>'']);

            $password = (new AdminService())->getPassword($password);


            $query = DB::table('admin')
                ->where('uniacid',$this->uniacid)
                ->where('account',$account)
                ->where('password',$password)
                ->where('del',1)
                ->first();

            if(!$query){
                return response(['code'=>40000,'message'=>'当前登录账号或当前登录密码输入错误']);
            }
            if($query->id!=$this->admin_id)return response(['code'=>40000,'message'=>'您输入的当前登录账号密码与当前登录用户不一致','data'=>'']);
            $query_inc = [
                'update_at'=>time()
            ];
            if($account2){
                if(strlen($account2)<5)return response(['code'=>40000,'message'=>'账号不能少于5位数']);
                $query_account = DB::table('admin')
                    ->where('uniacid',$this->uniacid)
                    ->where('account',$account2)
                    ->where('del',1)
                    ->first();
                if($account!=$account2 && $query_account)return response(['code'=>40000,'message'=>'该账号无法使用，请更换']);
                $query_inc['account'] = $account2;
            }
            if($password2){
                if(strlen($password2)<6)return response(['code'=>40000,'message'=>'密码不能少于6位数']);
                $password2 = (new AdminService())->getPassword($password2);
                $query_inc['password'] = $password2;
            }
            DB::table('admin')->where('id',$query->id)->update($query_inc);
        }

        return response(['code'=>20000,'message'=>'1','data'=>$res]);
    }
}
