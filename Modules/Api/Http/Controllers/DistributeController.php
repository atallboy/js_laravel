<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/8/24 21:54
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\balance\BalanceRecord;
use Modules\Admin\Services\user\UserService;

class DistributeController extends ApiController
{
    public function qrcodeBind(Request $request){
        if(!$this->distribute_mch_id)return $this->apiError('您还不是分销商，无法操作');
        $redeem_code = $request->input('redeem_code');
        $qrcode = DB::table('distribute_qrcode')->where('distribute_mch_id',$this->distribute_mch_id)->where('del',1)->value('pic');
        if($qrcode)return $this->apiError('您已经有分销码，无法再次兑换');
        $distribute_qrcode = DB::table('distribute_qrcode')->where('serial_number',$redeem_code)->where('status',0)->where('del',1)->first();
        if(!$distribute_qrcode)return $this->apiError('二维码编号错误');
        $res = DB::table('distribute_qrcode')->where('id',$distribute_qrcode->id)->update(['distribute_mch_id'=>$this->distribute_mch_id,'status'=>1,'bind_at'=>time()]);

        return $this->apiSuccess('',$res);
    }

    public function order(){

//        $list = DB::table('user')


        $query = DB::table('balance_record')->where('user_id',$this->user_id)->where('del',1)
            ->where('cate','>',30)
            ->where('cate','<',34)
            ->orderBy('id','desc');
        $list = $query->get();
        foreach ($list as $k=>$v){
            $list[$k]->create_at = date('Y-m-d H:i:s',$v->create_at);
            $msg = BalanceRecord::checkBalanceTypeDesc($v->cate);
            if($msg['describe']=='一级分销佣金')$list[$k]->pre_level = '一级分销';
            if($msg['describe']=='二级分销佣金')$list[$k]->pre_level = '二级分销';
            if($msg['describe']=='三级分销佣金')$list[$k]->pre_level = '三级分销';
            $order = DB::table('order')->where('id',$v->_id)->first();
            $item = DB::table('order_product')
                ->join('item','order_product.item_id','item.id')
                ->where('order_product.order_id',$v->_id)
                ->where('order_product.del',1)
//                ->select(['item.name','item.pic'])
                ->first();
            $item->pic = explode('&',$item->pic);
            $item_pic = $item->pic[0];
            $user = DB::table('user')->where('id',$order->user_id)->first();
            $list[$k]->order = ['product_fee'=>$order->product_fee,'item'=>$item->name,'item_pic'=>$item_pic];
            $list[$k]->pre_user = ['nickName'=>$user->nickName,'avatarUrl'=>$user->avatarUrl];
        }
        return $this->apiSuccess('',$list);
    }
    public function distributeMchInfo(){
        $data = DB::table('distribute_mch')->where('uniacid',$this->uniacid)->where('user_id',$this->user_id)->orderBy('id','desc')->first();
        if($data && $data->del==0)$data=false;
        return $this->apiSuccess('',$data?$data:false);
    }

    public function editDistributeMch(Request $request){
        $province = $request->input('province');
        $city = $request->input('city');
        $district = $request->input('district');
        $inc = [
            'name'=>$request->input('name'),
            'tel'=>$request->input('tel'),
            'apply_reason'=>$request->input('apply_reason'),
//            'role'=>$request->input('role'),
            'update_at'=>time()
        ];
        $inc['province'] = $province;
        $inc['city'] = $city;
        $inc['district'] = $district;

        $redeem_code = $request->input('serial_number');


        if(!$this->distribute_mch_id){
            $inc['role'] = $request->input('role');
            $check = DB::table('setting_distribute')->where('uniacid',$this->uniacid)->where('del',1)->value('mch_need_check');
            if($check==1){
                $inc['status'] = 0;
                $code = 0;
                $msg = '已提交申请，请等待平台审核';
                $qrcode_status = 0;
            }
            else{
                $inc['status'] = 1;
                $code = 1;
                $msg = '你已成为分销商，即将前往分销中心';
                $qrcode_status = 1;
            }
            $id = DB::table('distribute_mch')->insertGetId($this->cInc($inc));


            $distribute_qrcode = DB::table('distribute_qrcode')->where('serial_number',$redeem_code)->where('status',0)->where('del',1)->first();
            if($distribute_qrcode && !$distribute_qrcode->distribute_mch_id){
                DB::table('distribute_qrcode')->where('id',$distribute_qrcode->id)->update(['distribute_mch_id'=>$id,'status'=>$qrcode_status,'bind_at'=>time()]);
            }

//            return $this->apiError($qrcode);

        }else{
            $inc['status'] = 1;
            $code = 0;
            DB::table('distribute_mch')->where('id',$this->distribute_mch_id)->update($inc);
            $msg = '保存成功';
        }

        return $this->apiSuccess($msg,$code);
    }

    public function getInviteQrcode(Request $request){

        $refresh = $request->input('refresh');
        if($refresh || !$this->user->qrcode_gzh){
            $model = new UserService();
            $qrcode = $model->getLocalQrcode($this->user,$this->host);
            $avatar = $model->getLocalAvatar($this->user);
            $setting = DB::table('setting_distribute')->where('uniacid',$this->uniacid)->where('del',1)->first();
            $param = [
                'qrcode'=>$qrcode,
                'avatar'=>$avatar,
                'text'=>$this->user->nickName,
                'background_pic'=>$setting->background_pic,
                'position_x'=>$setting->position_x,
                'position_y'=>$setting->position_y,
                'position_x_name'=>$setting->position_x_name,
                'position_y_name'=>$setting->position_y_name,
                'position_x_avatar'=>$setting->position_x_avatar,
                'position_y_avatar'=>$setting->position_y_avatar,
                'save_path'=>'create/qrcode/user/'.md5(rand(10000,99999).time()).'.png'
            ];
//            return $this->apiSuccess('',$param);
            $path = $model->mergeImages($param);
            DB::table('user')->where('id',$this->user_id)->update(['qrcode_gzh'=>$param['save_path'],'update_at'=>time()]);
        }

        $qrcode_gzh = $this->host.'/'.DB::table('user')
            ->where('id',$this->user_id)
            ->where('del',1)
            ->value('qrcode_gzh').'?i='.time();


        return $this->apiSuccess('',$qrcode_gzh);
    }

    public function getInviteData(Request $request){
        $first = $second = $third = [];
        $data = ['first'=>$first,'second'=>$second,'third'=>$third];

        $data['info'] = $this->distribute_mch;
        $qrcode = DB::table('distribute_qrcode')->where('distribute_mch_id',$this->distribute_mch_id)->where('del',1)->value('pic');
        if($qrcode){
            $data['hotel_qrcode'] = $this->host.$qrcode;
        }

        $distribute_mch_performance = DB::table('distribute_mch_performance')->where('distribute_mch_id',$this->distribute_mch_id)->where('del',1)->first();
//        $data['performance'] = $distribute_mch_performance;

        $first = DB::table('user')->where('pre_id',$this->user_id)->where('del',1)->orderBy('id','desc')->get();
        foreach ($first as $k=>$v){
            $first[$k]->create_at = date('Y/m/d H:i:s',$v->create_at);
            $second = DB::table('user')->where('pre_id',$v->id)->where('del',1)->orderBy('id','desc')->get();
            foreach ($second as $k1=>$v1){
                $second[$k1]->create_at = date('Y/m/d H:i:s',$v1->create_at);
                $third = DB::table('user')->where('pre_id',$v1->id)->where('del',1)->orderBy('id','desc')->get();
                foreach ($third as $k2=>$v2){
                    $third[$k2]->create_at = date('Y/m/d H:i:s',$v2->create_at);
                }
            }
        }
        $level = DB::table('setting_distribute')->where('uniacid',$this->uniacid)->where('del',1)->value('level');
        $data['level'] = $level;
        if($level>0){$data['first'] = $first;$data['invite_num'] = count($first);$data['invite_num_first'] = count($first);}
        if($level>1){$data['second'] = $second;$data['invite_num'] += count($second);$data['invite_num_second'] = count($second);}
        if($level>2){$data['third'] = $third;$data['invite_num'] += count($third);$data['invite_num_third'] = count($third);}


        return $this->apiSuccess('',$data);
    }

}
