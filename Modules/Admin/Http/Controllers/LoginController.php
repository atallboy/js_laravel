<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/10/13 18:04
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Admin\Services\CommonService;
use Modules\Admin\Services\log\LogServices;
use Modules\Admin\Services\login\LoginService;

class LoginController extends CommonService
{
    public function login(Request $request){
        $account = $request->input('username');
        $password = $request->input('password');
        $validator = Validator::make($request->only(['username','password']),['username' => 'required','password' => 'required',],['account.required'=>'账号不可缺少','password.required'=>'密码不可缺少']);
        if ($validator->fails()) {return $this->apiError($this->validateErrorMsg($validator),$account,400); }

        $log = new LogServices();
        $log->uniacid = $this->uniacid;
        $log->event = 'adminLogin';
        $log->user_id = -1;
        $log->remark = $account;
        $content='登录成功';
        $password = md5($password.env('ADMIN_PASSWORD_SALT'));
        $query = DB::table('admin')
            ->where('account',$account)
            ->where('password',$password)
            ->where('del',1)
            ->first();
        if(!$query){
            $content = '账号或密码错误';
            $log->content = $account.'|'.$content.':登录ip：'.$request->ip();
            $log->saveLog();
            return $this->apiError('账号或密码错误',[],400);
        }
        if($query->status!=1){
            $content = '账号已被禁用，请联系管理员';
            $log->content = $account.'|'.$content.':登录ip：'.$request->ip();
            $log->saveLog();
            return $this->apiError('账号已被禁用，请联系管理员',[],400);
        }


        (new LoginService())->login($query);
        $session = session(env('ADMIN_TOKEN_NAME'));

        $log->content = $account.'|'.$content.':登录ip：'.$request->ip();
        $log->saveLog();
        return $this->apiSuccess();
    }

    public function logout(Request $request){
        $request->session()->flush();
        return $this->apiSuccess();
    }
}
