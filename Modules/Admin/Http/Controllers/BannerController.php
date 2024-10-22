<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/20 13:15
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Services\banner\BannerService;

class BannerController extends BaseController
{
    function index(Request $request)
    {
        return (new BannerService())->index($request->only(['page','limit','status','name']));
    }
    function edit(Request $request)
    {
        return (new BannerService())->edit($request->only(['status','id','name','cate','param','url','_sort','pic']));
    }

    public function del(Request $request){
        return (new BannerService())->del($request->input('id'));
    }
}
