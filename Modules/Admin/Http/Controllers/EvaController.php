<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/9/15 10:18
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Services\eva\EvaService;
use Modules\Api\Http\Controllers\ApiController;

class EvaController extends ApiController
{
    function index(Request $request)
    {
        return (new EvaService())->index($request->only(['page','limit','status','master_name','nickName','item_name']));
    }

    function edit(Request $request)
    {
        return (new EvaService())->edit($request->only(
            ['status','id','name','pic','store_name','gender','detail','base_review','base_collect']));
    }

    public function del(Request $request){
        return (new EvaService())->del($request->input('id'));
    }
}
