<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/8/18 22:23
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Services\mch\MchService;

class MchController extends BaseController
{
    function index(Request $request)
    {
        return (new MchService())->index($request->only(['page','limit','status','name']));
    }

    function edit(Request $request)
    {
        return (new MchService())->edit($request->only(
            [   'status','id','name','tel','pic','desc','detail','base_review','base_collect',
                'start_time','end_time','score','is_recommend','is_excellent',
                'is_hot','open_status',
                'province','city','district','address','latitude','longitude',
            ]));
    }

    public function del(Request $request){
        return (new MchService())->del($request->input('id'));
    }
}
