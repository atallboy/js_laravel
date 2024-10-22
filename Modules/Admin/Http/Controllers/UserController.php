<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/6/9 17:06
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\user\UserService;

class UserController extends BaseController
{

    function index(Request $request)
    {
        return (new UserService())->index($request->only(['page','limit','status','nickName','getcate','only_tel','tel']));
    }

    public function del(Request $request){
        return (new UserService())->del($request->input('id'));
    }

    public function reCreateQrcode(Request $request){
        return (new UserService())->createQrcode($request->input('id'),$this->host);
    }
}

