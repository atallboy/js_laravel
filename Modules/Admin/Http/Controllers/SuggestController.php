<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/19 17:21
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Services\suggest\SuggestService;

class SuggestController extends BaseController
{
    function index(Request $request)
    {
        return (new SuggestService())->index($request->only(['page','limit','status','name']));
    }
    function edit(Request $request)
    {
        return (new SuggestService())->edit($request->only(
            ['status','id','name','pic','remark','gender','detail','base_review','base_collect']));
    }

    public function del(Request $request){
        return (new SuggestService())->del($request->input('id'));
    }
}
