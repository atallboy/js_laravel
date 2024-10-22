<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/22 17:14
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Services\agent\AgentService;

class AgentController extends BaseController
{
    function index(Request $request)
    {
        return (new AgentService())->index($request->only(['page','limit','status','name']));
    }
    function edit(Request $request)
    {
        return (new AgentService())->edit($request->only(['status','id','name','cate','param','url','_sort','pic']));
    }

    public function del(Request $request){
        return (new AgentService())->del($request->input('id'));
    }
}
