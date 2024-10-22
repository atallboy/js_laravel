<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/19 15:12
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Services\order\OrderService;
use Modules\Common\Models\Pay;

class OrderController extends BaseController
{
    function index(Request $request)
    {
//        $pay = new Pay();
//        $pay->uniacid = $this->uniacid;
//        $pay->queryPaylogByCircle();
        return (new OrderService())->index($request->only(['page','limit','status','name','master_name']));
    }
    function edit(Request $request)
    {
        return (new OrderService())->edit($request->only(
            ['status','id','name','pic','store_name','gender','detail','base_review','base_collect']));
    }

    public function del(Request $request){
        return (new OrderService())->del($request->input('id'));
    }

    public function changeOrderMaster(Request $request){
        return (new OrderService())->changeOrderMaster($request->only(['master_id','order_id']));
    }

    public function refundOrder(Request $request){
        return (new OrderService())->refundOrder($request->only(['check_status','refund_money','order_id','remark','refund_id']));
    }

}
