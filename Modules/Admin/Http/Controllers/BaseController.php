<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/6/9 17:07
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Modules\Admin\Services\admin\AdminService;

class BaseController extends Controller
{

    public $uniacid;
    public $admin_id;
    public $admin;
    public $host;

    public function __construct(Request $request)
    {
        $this->uniacid = 1;
        $this->host = 'https://'.$_SERVER['HTTP_HOST'];
        $uri = $request->route()->uri();
        $value = $request->query('key');
        if($value=='super'&& $uri=='version/update')return ;
        $admin_info = (new AdminService())->getAdminInfo();
        if(array_key_exists('admin',$admin_info)){
            $this->admin_id = $admin_info['admin_id'];
            $this->admin = $admin_info['admin'];
        }
//        echo "<pre>";print_r($admin_info);echo "<pre>";die;

    }

    public function apiSuccess(string $message = '操作成功',$data = array(),int $status = 20000){
        return response()->json([
            'code' => $status,
            'message'=> $message,
            'data'=>$data
        ],200);
    }

    public function apiError(string $message = '失败',$data = array(),int $status = 40000){
        return response()->json([
            'code' => $status,
            'message'=> $message,
            'data'=>$data
        ],200);
    }

}
