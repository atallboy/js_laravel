<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/9/29 13:39
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\distribute\DistributeQrcodeService as service;

class DistributeQrcodeController extends BaseController
{
    function index(Request $request)
    {
        return (new service())->index($request->only(['create_record_id','page','limit','status','name']));
    }

    function edit(Request $request)
    {
        return (new service())->edit($request->only(['status','id','name','tel']));
    }

    public function del(Request $request){
        return (new service())->del($request->input('id'));
    }

    function create(Request $request)
    {
        return (new service())->create($request->only(['num','remark','percent_first','percent_second','percent_third','background_pic','position_x','position_y','position_x_remark','position_y_remark']),$this->host,$this->admin_id);
    }

    function download(Request $request)
    {

        $pic = $request->input('pic');
        $arr = explode('&',$pic);
        foreach ($arr as $k=>$v){
            $path = DB::table('distribute_qrcode')->where('id',$v)->where('del',1)->value('pic');
        }

        // 图片在服务器上的路径
        $filePath = public_path($path);
//        echo $filePath;die;

        // 检查文件是否存在
        if (file_exists($filePath)) {
            return response()->download($filePath, 'example.jpg', [
                'distributeQrcode/download' => 'Custom Value',
                'Content-Type'  => 'image/jpeg',
            ]);
        } else {
            return response()->json(['message' => '文件不存在'], 404);
        }
    }

}
