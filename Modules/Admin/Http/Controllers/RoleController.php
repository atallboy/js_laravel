<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/8/5 13:53
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;
use Modules\Admin\Services\role\RoleService;
use Illuminate\Http\Request;
class RoleController extends BaseController
{

    function index(Request $request)
    {
        return (new RoleService())->index($request->only(['page','limit','status','name']));
    }
    function edit(Request $request)
    {
        return (new RoleService())->edit($request->only(['status','id','name','desc','privilege']));
    }

    public function del(Request $request){
        return (new RoleService())->del($request->input('id'));
    }

    function privilege(Request $request)
    {
        return (new RoleService())->getPrivilege($request->only(['page','limit','status','name']));
    }

}
