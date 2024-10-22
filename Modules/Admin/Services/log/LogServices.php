<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/7/26 17:29
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\log;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Log;
use Modules\Admin\Services\BaseService;

class LogServices extends BaseService
{
    public function getLogStatusCode($status){
        $arr = [
            'adminLogin'=>1,
            'sendSubscribeMessage'=>117,
            'updateDBVersion'=>157,
            'orderDo'=>75,
            'orderLog'=>76,
            'autoRun'=>55,
            'privacyBind'=>51,
            'privacyCall'=>52,
            'privacyNotify'=>53,
            'sendSmsCode'=>62,
            'testSendSms'=>63,
            'sendSmsOrderNotice'=>64,
            'payNotify'=>130,
            'wechatPaySuccess'=>131,
            'wechatPayFail'=>132,
        ];
        if(!array_key_exists($status,$arr))return -2;
        return $arr[$status];
    }

    public function getLogCodeMessage($code=false){
        $arr = [
            '1'=>'后台登录',
            '117'=>'模板消息下发',
            '157'=>'版本更新',
            '75'=>'订单操作',
            '55'=>'系统自动执行',
            '51'=>'隐私电话绑定',
            '52'=>'隐私电话拨打',
            '53'=>'隐私电话语音通知',
            '62'=>'发送验证码',
            '63'=>'短信发送测试',
            '64'=>'订单短信通知',
            '130'=>'微信支付回调',
            '131'=>'微信支付回调-订单状态更新成功',
            '132'=>'微信支付回调-订单状态更新失败',
        ];
        if(!$code)return $arr;
        if(!array_key_exists($code,$arr))return '--';
        return $arr[$code];
    }

    public $uniacid;
    public $user_id;
    public $event;
    public $remark;
    public $content;
    public $detail;
    public $need_json;

    public function saveLog(){

        $code = $this->getLogStatusCode($this->event);
        $data = [
            'uniacid'=>$this->uniacid,
            'user_id'=>$this->user_id,
            'cate'=>$code,
            'remark'=>$this->remark?$this->remark:'',
            'content'=>$this->need_json==false?$this->content:json_encode($this->content),
            'create_at'=>time()];
        if($this->detail){
            $data['detail'] = $this->detail;
        }
        DB::table('log')
            ->insert($data);
    }


    public function index(array $data){

        $model = Log::query();
        $model = $this->queryCondition($model,$data,['cate']);
        $list = $model->select('*')
            ->where('del',1)
//            ->where('uniacid',$this->uniacid)
//            ->with(['user:*'])
            ->orderBy('id','desc')
            ->paginate(10)
            ->toArray();

        foreach ($list['data'] as $k=>$v){
            $list['data'][$k]['cate'] = $this->getLogCodeMessage($v['cate']);
        }

        return $this->apiSuccess('',[
            'list'=>$list['data'],
            'total'=>$list['total']
        ]);
    }

    public function edit(array $data){
        $model = new Log();
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
        $model = new Log();
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

}
