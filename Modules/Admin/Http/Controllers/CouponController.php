<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/20 20:58
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Services\coupon\CouponService;

class CouponController extends BaseController
{
    function index(Request $request)
    {
        return (new CouponService())->index($request->only(['page','limit','status','name']));
    }
    function edit(Request $request)
    {
        return (new CouponService())->edit($request->only(
            ['status','id','name','amount','get_type','use_range','type','minimum',
                'valid_time_type','valid_day','valid_start_time','valid_end_time','get_limit','redeem_code']));
    }

    public function del(Request $request){
        return (new CouponService())->del($request->input('id'));
    }
}
