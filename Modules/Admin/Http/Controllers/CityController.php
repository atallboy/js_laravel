<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/9/30 22:29
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Services\city\CityService;

class CityController extends BaseController
{
    function index(Request $request)
    {
        return (new CityService())->index($request->only(['page','limit','status','name']));
    }

    function edit(Request $request)
    {
        return (new CityService())->edit($request->only(['status','id','name','type','_sort']));
    }

    public function del(Request $request){
        return (new CityService())->del($request->input('id'));
    }
}
