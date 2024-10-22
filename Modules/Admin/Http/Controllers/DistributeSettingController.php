<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/8/23 22:44
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\distribute\DistributeSettingService;
use Intervention\Image\Facades\Image;
use Modules\Admin\Services\user\UserService;

class DistributeSettingController extends BaseController
{
    function index(Request $request)
    {
        return (new DistributeSettingService())->index($request->only(['page','limit','status','name']));
    }

    function edit(Request $request)
    {
        return (new DistributeSettingService())->edit($request->only(['id','level','status','mch_need_check','percent_first','percent_second','percent_third','background_pic',
            'position_x','position_y','position_x_name','position_y_name','position_x_avatar','position_y_avatar']));
    }


    public function mergeImages(Request $request)
    {

        $background_pic = $request->input('background_pic');
        $position_x = $request->input('position_x');
        $position_y = $request->input('position_y');
        $position_x_name = $request->input('position_x_name');
        $position_y_name = $request->input('position_y_name');
        $position_x_avatar = $request->input('position_x_avatar');
        $position_y_avatar = $request->input('position_y_avatar');

        $user = DB::table('user')->where('uniacid',$this->uniacid)->orderBy('id','asc')->where('id','>',1)->where('del',1)->first();

        $model = new UserService();
        $qrcode = $model->getLocalQrcode($user,$this->host);
        $avatar = $model->getLocalAvatar($user);

        $param = [
            'qrcode'=>$qrcode,
            'avatar'=>$avatar,
            'text'=>$user->nickName,
            'background_pic'=>$background_pic,
            'position_x'=>$position_x,
            'position_y'=>$position_y,
            'position_x_name'=>$position_x_name,
            'position_y_name'=>$position_y_name,
            'position_x_avatar'=>$position_x_avatar,
            'position_y_avatar'=>$position_y_avatar,
            'save_path'=>'create/qrcode/user/'.md5(rand(10000,99999).time()).'.png'
        ];
//            return $this->apiSuccess('',$param);
        $path = $model->mergeImages($param);

        return $this->apiSuccess('',$this->host.'/'.$param['save_path']);
    }

}



