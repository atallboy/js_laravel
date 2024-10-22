<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/8 15:58
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Services\item\ItemService;

class ItemController extends BaseController
{
    function index(Request $request)
    {
        return (new ItemService())->index($request->only(['page','limit','status','name']));
    }
    function edit(Request $request)
    {
        return (new ItemService())->edit($request->only(['status','id','name','desc','cate_id','price','old_price','pic','long_time','tag','range_people','gender','detail','base_sale','base_collect']));
    }

    public function del(Request $request){
        return (new ItemService())->del($request->input('id'));
    }
}
