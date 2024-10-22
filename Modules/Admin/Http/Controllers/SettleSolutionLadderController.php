<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/8/3 12:22
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Services\settle\SettleSolutionLadderService;

class SettleSolutionLadderController extends BaseController
{
    function index(Request $request)
    {
        return (new SettleSolutionLadderService())->index($request->only(['page','limit','status','settle_solution_id']));
    }
    function edit(Request $request)
    {
        return (new SettleSolutionLadderService())->edit($request->only(['status','id','fee_type','min','max','percent','settle_solution_id']));
    }

    public function del(Request $request){
        return (new SettleSolutionLadderService())->del($request->input('id'));
    }
}
