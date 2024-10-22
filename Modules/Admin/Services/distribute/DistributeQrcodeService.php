<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/9/29 13:44
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\distribute;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\DistributeQrcode as Model;
use Modules\Admin\Services\BaseService;
use Modules\Admin\Services\user\UserService;
use Modules\Common\Models\Doer;

class DistributeQrcodeService extends BaseService
{

    public function create(array $data,$host,$admin_id){

        if($data['num']<1 || $data['num']>200)return $this->apiError('每次生成数量需要1-200之间');

        $inc = [
            'uniacid'=>$this->uniacid,
            'admin_id'=>$admin_id,
            'create_at'=>time(),
            'num'=>$data['num'],
            'remark'=>$data['remark'],
            'percent_first'=>$data['percent_first'],
            'percent_second'=>$data['percent_second'],
            'percent_third'=>$data['percent_third'],
            'background_pic'=>array_key_exists('background_pic',$data)?$data['background_pic']:'',
            'position_x'=>array_key_exists('position_x',$data)?$data['position_x']:0,
            'position_y'=>array_key_exists('position_y',$data)?$data['position_y']:0,
            'position_x_remark'=>array_key_exists('position_x_remark',$data)?$data['position_x_remark']:0,
            'position_y_remark'=>array_key_exists('position_y_remark',$data)?$data['position_y_remark']:0,
        ];
        if(array_key_exists('background_pic',$data)){
            $background_pic = str_replace($host.'/','',$data['background_pic']);
            $inc['background_pic'] = $background_pic;
        }
        $_id = DB::table('qrcode_create_record')->insertGetId($inc);
//        return $this->apiSuccess('',$inc);
        for ($i=0;$i<$data['num'];$i++){
            $serial_number = DB::table('distribute_qrcode')
                ->where('del', 1)
                ->orderBy('id','desc')
                ->value('id');
            $serial_number = $serial_number+1000;


            $token = Doer::getRandChar(6);
            $distribute_qrcode = DB::table('distribute_qrcode')->where('serial_number',$token)->where('del',1)->first();
            if($distribute_qrcode)continue;

            $serial_number = $token;

            $pic = (new UserService())->createDistributeQrcode(['file_name'=>$token,'serial_number'=>$serial_number,'uniacid'=>$this->uniacid,'host'=>$host]);


            if($inc['background_pic']){
                if(!file_exists(public_path('create/qrcode/distribute'))){
                    if (!mkdir(public_path('create/qrcode/distribute'), 0777, true)) {
                        return 'Failed to create directories';
                    }
                }
                $param = [
                    'qrcode'=>public_path($pic),
                    'text'=>$data['remark'],
                    'background_pic'=>public_path($inc['background_pic']),
                    'position_x'=>$inc['position_x'],
                    'position_y'=>$inc['position_y'],
                    'position_x_name'=>$inc['position_x_remark'],
                    'position_y_name'=>$inc['position_y_remark'],
                    'save_path'=>'/create/qrcode/distribute/'.md5(rand(10000,99999).time()).'.png'
                ];
//                return $this->apiSuccess('',$param);
                $pic = (new UserService())->mergeImages($param);
            }


            $inc_qrcode = [
                'uniacid'=>$this->uniacid,
                'create_record_id'=>$_id,
                'serial_number'=>$serial_number,
                'token'=>$token,
                'pic'=>$pic,
                'remark'=>$data['remark'],
                'create_at'=>time()
            ];
            DB::table('distribute_qrcode')->insert($inc_qrcode);
        }


        return $this->apiSuccess('');
    }

    public function index(array $data){

        $model = Model::query();
        $model = $this->queryCondition($model,$data,['create_record_id']);
        $list = $model->select('*')
            ->where('del',1)
            ->where('uniacid',$this->uniacid)
//            ->with(['user:*'])
            ->orderBy('id','desc')
            ->paginate(10)
            ->toArray();

        foreach ($list['data'] as $k=>$v){
            if($v['bind_at'])$list['data'][$k]['bind_at'] = date('Y-m-d H:i:s',$v['bind_at']);
            $list['data'][$k]['pic'] = 'https://'.$_SERVER['HTTP_HOST'].$v['pic'];
            $list['data'][$k]['distribute_mch'] = [];
            if($v['distribute_mch_id']){
                $list['data'][$k]['distribute_mch'] = DB::table('distribute_mch')->where('id',$v['distribute_mch_id'])->first();
            }
        }

        return $this->apiSuccess('',[
            'list'=>$list['data'],
            'total'=>$list['total']
        ]);
    }

    public function edit(array $data){
        $model = new Model();
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
        $model = new Model();
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
