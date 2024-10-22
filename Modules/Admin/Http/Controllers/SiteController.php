<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/8/22 18:28
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Services\site\SiteService;

class SiteController extends BaseController
{
    function index(Request $request)
    {
        return (new SiteService())->index($request->only(['page','limit','status','name']));
    }
    function edit(Request $request)
    {
        return (new SiteService())->edit($request->only(['status','id','name','desc','pic']));
    }

    public function del(Request $request){
        return (new SiteService())->del($request->input('id'));
    }
}
