<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/8/3 12:21
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Services\settle\SettleSolutionService;

class SettleSolutionController extends BaseController
{
    function index(Request $request)
    {
        return (new SettleSolutionService())->index($request->only(['page','limit','status','settle_solution_id']));
    }
    function edit(Request $request)
    {
        return (new SettleSolutionService())->edit($request->only(
            ['status','id','name','settle_object','settle_time','settle_type','master_id','agent_id']));
    }

    public function del(Request $request){
        return (new SettleSolutionService())->del($request->input('id'));
    }
}
