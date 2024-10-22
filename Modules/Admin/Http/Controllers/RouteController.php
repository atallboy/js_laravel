<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/8/7 15:19
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Services\admin\AdminService;

class RouteController extends BaseController
{
    public function index(Request $request){


        $data = [
            [
                'path'=>'/','component'=>'Layout','name'=>'Summary','meta'=>[ 'title'=>'概览', 'icon'=> 'dashboard'],
                'children'=>[['path'=>'','component'=>'jishi/summary/index','name'=>'Summary','meta'=>[ 'title'=>'概览', 'icon'=> 'dashboard', 'noCache'=> true ]]]
            ],
            [
                'path'=>'/order','component'=>'Layout','name'=>'Order','meta'=>[ 'title'=>'订单管理', 'icon'=> 'component'],
                'children'=>[['path'=>'list','component'=>'jishi/order/list','name'=>'OrderList','meta'=>[ 'title'=>'订单列表', 'noCache'=> true ]]]
            ],
            [
                'path'=>'/jiazhongorder','component'=>'Layout','name'=>'JiaZhongOrder','meta'=>[ 'title'=>'加钟订单', 'icon'=> 'peoples'],
                'children'=>[['path'=>'list','component'=>'jishi/jiazhongorder/jiazhonglist','name'=>'JiaZhongOrderList','meta'=>[ 'title'=>'加钟订单列表', 'icon'=> 'el-icon-s-order', 'noCache'=> true ]]]
            ],
            [
                'path'=>'/eva','component'=>'Layout','name'=>'Order','meta'=>[ 'title'=>'订单评价', 'icon'=> 'component'],
                'children'=>[['path'=>'list','component'=>'jishi/order/eva','name'=>'EvaList','meta'=>[ 'title'=>'评价管理', 'noCache'=> true ]]]
            ],
            [
                'path'=>'/withdrawal','component'=>'Layout','name'=>'Withdrawal','meta'=>[ 'title'=>'提现管理', 'icon'=> 'money'],
                'children'=>[['path'=>'list','component'=>'jishi/withdrawal/record','name'=>'WithdrawalList','meta'=>[ 'title'=>'提现列表', 'icon'=> 'money', 'noCache'=> true ]]]
            ],
            [
                'path'=>'/user','component'=>'Layout','name'=>'User','meta'=>[ 'title'=>'用户管理', 'icon'=> 'peoples'],
                'children'=>[['path'=>'list','component'=>'jishi/user/list','name'=>'UserList','meta'=>[ 'title'=>'用户列表', 'icon'=> 'peoples', 'noCache'=> true ]]]
            ],
            [
                'path'=>'/user','component'=>'Layout','name'=>'User','meta'=>[ 'title'=>'号码管理', 'icon'=> 'peoples'],
                'children'=>[['path'=>'telist','component'=>'jishi/user/tel','name'=>'UserList','meta'=>[ 'title'=>'手机号列表', 'icon'=> 'peoples', 'noCache'=> true ]]]
            ],
            [
                'path'=>'/master','component'=>'Layout','name'=>'Master','meta'=>[ 'title'=>'技师管理', 'icon'=> 'people'],
                'children'=>[['path'=>'list','component'=>'jishi/master/list','name'=>'MasterList','meta'=>[ 'title'=>'技师列表', 'noCache'=> true ]]]
            ],
            [
                'path'=>'/mch','component'=>'Layout','name'=>'Mch','meta'=>[ 'title'=>'商户管理', 'icon'=> 'user'],
                'children'=>[['path'=>'list','component'=>'jishi/mch/list','name'=>'MchList','meta'=>[ 'title'=>'商户列表', 'noCache'=> true ]]]
            ],
            [
                'path'=>'/agent','component'=>'Layout','name'=>'Agent','meta'=>[ 'title'=>'代理管理', 'icon'=> 'el-icon-s-custom'],
                'children'=>[['path'=>'list','component'=>'jishi/agent/list','name'=>'AgentList','meta'=>[ 'title'=>'代理列表','noCache'=> true ]]]
            ],
//            [
//                'path'=>'/zhubo','component'=>'Layout','name'=>'Zhubo','meta'=>[ 'title'=>'表单信息收集', 'icon'=> 'excel'],
//                'children'=>[['path'=>'zhubo','component'=>'jishi/master/zhubo','name'=>'ZhuboList','meta'=>[ 'title'=>'表单信息列表', 'noCache'=> true ]]]
//            ],
            [
                'path'=>'/item','component'=>'Layout','name'=>'Item','meta'=>[ 'title'=>'服务管理', 'icon'=> 'example'],
                'children'=>[['path'=>'list','component'=>'jishi/item/list','name'=>'ItemList','meta'=>[ 'title'=>'服务项目', 'noCache'=> true ]]]
            ],
            [
                'path'=>'/distribute','component'=>'Layout','name'=>'SettingDistributeApply','meta'=>[ 'title'=>'分销商', 'icon'=> 'example'],
                'children'=>[['path'=>'apply','component'=>'jishi/distribute/index','name'=>'SettingDistributeApply','meta'=>[ 'title'=>'分销商列表', 'noCache'=> true ]]]
            ],
            [
                'path'=>'/distributeHotel','component'=>'Layout','name'=>'DistributeHotel','meta'=>[ 'title'=>'酒店分销商', 'icon'=> 'example'],
                'children'=>[
                    ['path'=>'index','component'=>'jishi/distribute/hotel','name'=>'DistributeHotel','meta'=>[ 'title'=>'酒店分销商列表', 'noCache'=> true ]],
                    ['path'=>'qrcode','component'=>'jishi/distribute/batch','name'=>'DistributeHotelQrcode','meta'=>[ 'title'=>'酒店分销码', 'noCache'=> true ]],
                ]
            ],
            ['path'=>'/setting_distribute','component'=>'Layout','name'=>'SettingDistribute','meta'=>[ 'title'=>'分销设置', 'icon'=> 'el-icon-setting'],
                'children'=>[
//                    ['path'=>'apply','component'=>'jishi/distribute/apply','name'=>'SettingDistributeApply','meta'=>[ 'title'=>'分销商申请',  'noCache'=> true ]],
                    ['path'=>'index','component'=>'jishi/distribute/setting','name'=>'SettingDistributeIndex','meta'=>[ 'title'=>'基础设置',  'noCache'=> true ]],
                    ['path'=>'invite','component'=>'jishi/distribute/invite','name'=>'SettingDistributeInvite','meta'=>[ 'title'=>'邀请记录', 'noCache'=> true ]],
                ]
            ],
            [
                'path'=>'/city','component'=>'Layout','name'=>'City','meta'=>[ 'title'=>'城市管理', 'icon'=> 'el-icon-picture'],
                'children'=>[['path'=>'list','component'=>'jishi/city/list','name'=>'CityList','meta'=>[ 'title'=>'城市列表', 'noCache'=> true ]]]
            ],
            [
                'path'=>'/solution','component'=>'Layout','name'=>'Settle','meta'=>[ 'title'=>'结算管理', 'icon'=> 'el-icon-s-finance'],
                'children'=>[['path'=>'solution','component'=>'jishi/settle/solution','name'=>'SettleSolution','meta'=>[ 'title'=>'结算方案', 'noCache'=> true ]]]
            ],
            [
                'path'=>'/coupon','component'=>'Layout','name'=>'Coupon','meta'=>[ 'title'=>'优惠券管理', 'icon'=> 'el-icon-s-ticket'],
                'children'=>[['path'=>'list','component'=>'jishi/coupon/list','name'=>'CouponList','meta'=>[ 'title'=>'优惠券列表', 'noCache'=> true ]]]
            ],
            [
                'path'=>'/banner','component'=>'Layout','name'=>'Banner','meta'=>[ 'title'=>'广告管理', 'icon'=> 'el-icon-picture'],
                'children'=>[['path'=>'list','component'=>'jishi/banner/list','name'=>'BannerList','meta'=>[ 'title'=>'广告列表', 'noCache'=> true ]]]
            ],
            [
                'path'=>'/privacy','component'=>'Layout','name'=>'Sms','meta'=>[ 'title'=>'隐私电话', 'icon'=> 'example'],
                'children'=>[
                    ['path'=>'index','component'=>'jishi/setting/privacy_tel','name'=>'privacyIndex','meta'=>[ 'title'=>'隐私电话配置',  'noCache'=> true ]],
                ]
            ],
            [
                'path'=>'/suggest','component'=>'Layout','name'=>'Suggest','meta'=>[ 'title'=>'投诉建议', 'icon'=> 'form'],
                'children'=>[['path'=>'list','component'=>'jishi/suggest/list','name'=>'SuggestList','meta'=>[ 'title'=>'反馈列表', 'noCache'=> true ]]]
            ],

            ['path'=>'/setting','component'=>'Layout','name'=>'Setting','meta'=>[ 'title'=>'系统设置', 'icon'=> 'el-icon-setting'],
                'children'=>[
                    ['path'=>'index','component'=>'jishi/setting/index','name'=>'SettingIndex','meta'=>[ 'title'=>'基础设置',  'noCache'=> true ]],

                ]
            ],
            ['path'=>'/role','component'=>'Layout','name'=>'Setting','meta'=>[ 'title'=>'权限控制', 'icon'=> 'el-icon-setting'],
                'children'=>[
                    ['path'=>'role','component'=>'jishi/role/index','name'=>'RoleIndex','meta'=>[ 'title'=>'角色列表', 'noCache'=> true ]],
                    ['path'=>'admin','component'=>'jishi/admin/index','name'=>'AdminIndex','meta'=>[ 'title'=>'管理员列表', 'noCache'=> true ]],
                ]
            ],
            [
                'path'=>'/subscribe','component'=>'Layout','name'=>'Subscribe','meta'=>[ 'title'=>'模板消息', 'icon'=> 'el-icon-platform-eleme'],
                'children'=>[['path'=>'list','component'=>'jishi/subscribe/list','name'=>'SubscribeList','meta'=>[ 'title'=>'模板消息配置', 'noCache'=> true ]]]
            ],
            [
                'path'=>'/sms','component'=>'Layout','name'=>'Sms','meta'=>[ 'title'=>'短信服务', 'icon'=> 'example'],
                'children'=>[
                    ['path'=>'update','component'=>'jishi/setting/sms','name'=>'smsSetting','meta'=>[ 'title'=>'短信配置',  'noCache'=> true ]],
                ]
            ],
            [
                'path'=>'/log','component'=>'Layout','name'=>'Log','meta'=>[ 'title'=>'日志管理', 'icon'=> 'nested'],
                'children'=>[['path'=>'list','component'=>'jishi/log/list','name'=>'LogList','meta'=>[ 'title'=>'日志管理', 'noCache'=> true ]]]
            ],
            [
                'path'=>'/update','component'=>'Layout','name'=>'Update','meta'=>[ 'title'=>'系统更新', 'icon'=> 'example'],
                'children'=>[
                    ['path'=>'update','component'=>'jishi/setting/update','name'=>'versionUpdate','meta'=>[ 'title'=>'版本更新',  'noCache'=> true ]],
                ]
            ],
        ];


        $adminData = (new AdminService())->getAdminInfo();
        $arr = [

        ];
        $privilege = $adminData['privilege'];
        foreach ($data as $k=>$v){
//            echo "<pre>";print_r($v);echo "<pre>";
            if($v['meta']['title']=='概览'){$arr[] = $v;continue ;}
            $c = [];
            foreach ($v['children'] as $k1=>$v1){
                if(in_array($v1['meta']['title'],$privilege)){
                    $c[] = $v1;
                }
            }
            if($c)$arr[] = ['path'=>$v['path'],'component'=>$v['component'],'name'=>$v['name'],'meta'=>$v['meta'], 'children'=>$c];
        }


//        $arr = $data;
//        echo "<pre>";print_r($_SERVER['HTTP_HOST']);echo "<pre>";die;
        if($adminData['account']=='i77' || $adminData['privilege_cate']==1 || array_key_exists('HTTP_ORIGIN',$_SERVER))$arr = $data;


        return $this->apiSuccess('success',['routes'=>$arr]);
    }
}
