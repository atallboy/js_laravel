<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/8/5 13:54
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\role;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Role as model;
use Modules\Admin\Services\BaseService;

class RoleService extends BaseService
{
    public function getPrivilege(array $data){

        $arr = $this->getPrivilegeData();
        $data = [];
        foreach ($arr as $k=>$v){
            $c = [];
            foreach ($v['child'] as $k1=>$v1){
                $c[] = $v1['name'];
            }
            $v['child'] = $c;
            $data[] = $v;
        }

        return $this->apiSuccess('',$data);
    }

    public function index(array $data){

        $model = model::query();
        $model = $this->queryCondition($model,$data,[]);
        $list = $model->select('*')
            ->where('del',1)
            ->where('uniacid',$this->uniacid)
            ->orderBy('id','desc')
            ->paginate(10)
            ->toArray();

        foreach ($list['data'] as $k=>$v){
            $list['data'][$k]['privilege'] = explode('&',trim($v['privilege'], "&"));
        }

        return $this->apiSuccess('',[
            'list'=>$list['data'],
            'total'=>$list['total']
        ]);
    }

    public function edit(array $data){
        $data['privilege'] = '&'.$data['privilege'].'&';
        $model = new model();
        $_id = $this->commonCreateOrrUpdate($model,$data);
        DB::beginTransaction();
        try{
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            return $this->apiError('操作失败！');
        }
        return $this->apiSuccess('',[]);
    }

    public function del(int $id){
        $model = new model();
        DB::beginTransaction();
        try{
            $insert_id = $this->commonDel($model,$id);
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            $this->apiError('删除失败！');
        }
        return $this->apiSuccess('',[]);
    }

    public function getPrivilegeUri($privilege){
        $arr = $this->getPrivilegeData();
        $data = [];
        foreach ($arr as $k=>$v){
            foreach ($v['child'] as $k1=>$v1){
                if(in_array($v1['name'],$privilege))$data[] = $v1['uri'];
            }
        }
        return $data;
    }

    public function getPrivilegeData(){
        $data = [
            [
                'parent'=>['id'=>1,'name'=>'订单管理'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'订单列表','uri'=>'order/list'],['name'=>'订单编辑','uri'=>'order/edit'],['name'=>'订单删除','uri'=>'order/del'],['name'=>'订单退款','uri'=>'order/refund'],['name'=>'更换师傅','uri'=>'order/changeOrderMaster']]
            ],
            [
                'parent'=>['id'=>2,'name'=>'加钟订单'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'加钟订单列表','uri'=>'jiazhongorder/jiazhonglist'],['name'=>'加钟订单编辑','uri'=>''],['name'=>'加钟订单删除','uri'=>'jiazhongorder/jzdel']]
            ],
            [
                'parent'=>['id'=>3,'name'=>'提现管理'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'提现列表','uri'=>'withdrawal/list'],['name'=>'同意/拒绝提现','uri'=>'withdrawal/edit']]
            ],
            [
                'parent'=>['id'=>4,'name'=>'用户管理'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'用户列表','uri'=>'user/list'],['name'=>'用户编辑','uri'=>'user/edit'],['name'=>'用户删除','uri'=>'user/del'],['name'=>'刷新用户二维码','uri'=>'user/reCreateQrcode']]
            ],
            [
                'parent'=>['id'=>5,'name'=>'技师管理'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'技师列表','uri'=>'master/list'],['name'=>'技师编辑','uri'=>'master/edit'],['name'=>'技师删除','uri'=>'master/del']]
            ],
            [
                'parent'=>['id'=>6,'name'=>'商户管理'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'商户列表','uri'=>'mch/list'],['name'=>'商户编辑','uri'=>'mch/edit'],['name'=>'商户删除','uri'=>'mch/del']]
            ],
            [
                'parent'=>['id'=>7,'name'=>'代理管理'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'代理列表','uri'=>'agent/list'],['name'=>'代理编辑','uri'=>'agent/edit'],['name'=>'代理删除','uri'=>'agent/del']]
            ],
//            [
//                'parent'=>['id'=>9,'name'=>'表单信息收集'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
//                'child'=>[['name'=>'表单信息列表','uri'=>'/list'],['name'=>'表单信息编辑','uri'=>'/edit'],['name'=>'表单信息删除','uri'=>'/del']]
//            ],
            [
                'parent'=>['id'=>10,'name'=>'服务管理'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'服务项目','uri'=>'item/list'],['name'=>'服务项目编辑','uri'=>'item/edit'],['name'=>'服务项目删除','uri'=>'item/del']]
            ],
            [
                'parent'=>['id'=>11,'name'=>'结算管理'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'结算方案','uri'=>'settleSolution/list'],['name'=>'结算方案编辑','uri'=>'settleSolution/edit'],['name'=>'结算方案删除','uri'=>'settleSolution/del']]
            ],
            [
                'parent'=>['id'=>12,'name'=>'优惠券管理'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'优惠券列表','uri'=>'coupon/list'],['name'=>'优惠券编辑','uri'=>'coupon/edit'],['name'=>'优惠券删除','uri'=>'coupon/del']]
            ],
            [
                'parent'=>['id'=>13,'name'=>'广告管理'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'广告列表','uri'=>'banner/list'],['name'=>'广告编辑','uri'=>'banner/edit'],['name'=>'广告删除','uri'=>'banner/del']]
            ],
            [
                'parent'=>['id'=>14,'name'=>'投诉建议'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'反馈列表','uri'=>'suggest/list']]
            ],
            [
                'parent'=>['id'=>15,'name'=>'模板消息'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'模板消息配置','uri'=>'subscribe/list'],['name'=>'模板消息编辑','uri'=>'subscribe/edit'],['name'=>'模板消息删除','uri'=>'subscribe/del'],['name'=>'模板消息发送测试','uri'=>'subscribe/sendSubscribeTest']]
            ],
            [
                'parent'=>['id'=>16,'name'=>'日志管理'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'日志管理','uri'=>'log/list']]
            ],
            [
                'parent'=>['id'=>17,'name'=>'系统设置'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'基础设置','uri'=>'setting/index'],['name'=>'基础设置编辑','uri'=>'setting/edit']]
            ],
            [
                'parent'=>['id'=>18,'name'=>'角色列表'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'角色列表','uri'=>'role/list'],['name'=>'角色编辑','uri'=>'role/edit'],['name'=>'角色删除','uri'=>'role/del']]
            ],
            [
                'parent'=>['id'=>19,'name'=>'管理员列表'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'管理员列表','uri'=>'admin/list'],['name'=>'管理员编辑','uri'=>'admin/edit'],['name'=>'管理员删除','uri'=>'admin/del']]
            ],
            [
                'parent'=>['id'=>20,'name'=>'版本更新'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'版本更新','uri'=>'version/update']]
            ],
            [
                'parent'=>['id'=>21,'name'=>'分销商'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'分销商列表','uri'=>'distributeMch/index'],['name'=>'申请审批','uri'=>'distributeMch/approve'],['name'=>'编辑分销商','uri'=>'distributeMch/edit'],['name'=>'删除分销商','uri'=>'distributeMch/del'],]
            ],
            [
                'parent'=>['id'=>22,'name'=>'分销设置'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'分销基础设置','uri'=>'distributeSetting/index'],['name'=>'分销设置编辑','uri'=>'distributeSetting/edit'],['name'=>'分销二维码效果测试','uri'=>'distributeSetting/mergeImages']]
            ],
            [
                'parent'=>['id'=>23,'name'=>'邀请记录'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'邀请记录','uri'=>'distributeInvite/list']]
            ],
            [
                'parent'=>['id'=>24,'name'=>'订单评价'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'评价管理','uri'=>'eva/list']]
            ],
            [
                'parent'=>['id'=>25,'name'=>'短信服务'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'短信配置','uri'=>'sms/index']]
            ],
            [
                'parent'=>['id'=>26,'name'=>'酒店分销'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'酒店列表','uri'=>'distributeHotel/index'],['name'=>'酒店编辑','uri'=>'distributeHotel/edit'],['name'=>'酒店删除','uri'=>'distributeHotel/del']]
            ],
            [
                'parent'=>['id'=>27,'name'=>'酒店分销码'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'酒店分销码列表','uri'=>'distributeQrcode/index'],['name'=>'分销码生成','uri'=>'distributeQrcode/create'],['name'=>'分销码删除','uri'=>'distributeQrcode/del'],['name'=>'分销码下载','uri'=>'distributeQrcode/download']]
            ],
            [
                'parent'=>['id'=>28,'name'=>'城市管理'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'城市列表','uri'=>'city/index'],['name'=>'城市编辑','uri'=>'city/edit'],['name'=>'城市删除','uri'=>'city/del']]
            ],
            [
                'parent'=>['id'=>29,'name'=>'隐私电话'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'隐私电话配置','uri'=>'privacy/index'],['name'=>'隐私电话编辑','uri'=>'privacy/edit']]
            ],
            [
                'parent'=>['id'=>30,'name'=>'号码管理'],'checkAll'=>false,'checkedChildren'=>[],'isIndeterminate'=>false,
                'child'=>[['name'=>'手机号列表','uri'=>'user/tellist']]
            ],
        ];
        return $data;
    }


}
