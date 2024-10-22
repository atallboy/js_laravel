<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/17 08:19
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Services\master\MaterService;

class MasterController extends BaseController
{
    function index(Request $request)
    {
        return (new MaterService())->index($request->only(['page','limit','status','name','cate']));
    }

    function edit(Request $request)
    {
        return (new MaterService())->edit($request->only(
            [   'status','id','user_id','age','name','tel',
                'pic','idcard','certificate',
                'desc','store_name','gender','detail',
                'base_review','base_collect','base_order',
                'start_time','end_time','travel_expense','taxi_fee','qibujia','bus_fee','jiazhonglv','score','is_recommend','is_excellent',
                'is_hot','is_fast','open_status',
                'province','city','district','address','latitude','longitude',
                'master_service'
            ]));
    }

    public function del(Request $request){
        return (new MaterService())->del($request->input('id'));
    }
}
