<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/8/3 12:23
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Services\settle\SettleRecordService;

class SettleRecordController extends BaseController
{
    function index(Request $request)
    {
        return (new SettleRecordService())->index($request->only(['page','limit','status','name']));
    }
}
