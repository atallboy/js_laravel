<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/7/31 09:47
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Services\log\LogServices;

class LogController extends BaseController
{
    function index(Request $request)
    {
        return (new LogServices())->index($request->only(['page','limit','status','name','cate']));
    }

    function cate(Request $request)
    {

        $data = (new LogServices())->getLogCodeMessage();
        $list = [];
        foreach ($data as $k=>$v){
            $list[] = ['label'=>$v,'value'=>$k];
        }

        return $this->apiSuccess('',$list);
    }
}
