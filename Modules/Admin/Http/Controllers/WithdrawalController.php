<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/26 14:18
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Services\withdrawal\WithdrawalService;

class WithdrawalController extends BaseController
{
    function index(Request $request)
    {
        return (new WithdrawalService())->index($request->only(['page','limit','status','name','cate']));
    }

    function edit(Request $request)
    {
        return (new WithdrawalService())->edit($request->only(['user_id','money','status','remark','user_id']),$request->input('id'));
    }

    public function del(Request $request){
        return (new WithdrawalService())->del($request->input('id'));
    }
}
