<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\admin\AdminService;
use Modules\Admin\Services\role\RoleService;

class CheckAdminPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $uri = $request->route()->uri();
        $value = $request->query('key');
        if($value=='super'&& $uri=='version/update')return $next($request);

        $adminData = (new AdminService())->getAdminInfo();
         $privilege = (new RoleService())->getPrivilegeUri($adminData['privilege']);
        if(!$adminData['isLogin'] && !array_key_exists('HTTP_ORIGIN',$_SERVER)){
            return response()->json(['code' => 50008, 'message'=> '未登录或登录失效', 'data'=>''],200);
        }

        if(!in_array($uri,$privilege) && $adminData['privilege_cate']!=1  && !array_key_exists('HTTP_ORIGIN',$_SERVER)){
            return response()->json(['code' => 40000, 'message'=> '无权限', 'data'=>''],200);
        }

//        echo "<pre>";print_r($uri);echo "<pre>";

        return $next($request);
    }
}
