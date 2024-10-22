<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/7/30 09:05
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\subscribe;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\SubscribeMessage;
use Modules\Admin\Services\BaseService;

class SubscribeService extends BaseService
{
    public function index(array $data){

        $model = SubscribeMessage::query();
        $model = $this->queryCondition($model,$data,[]);
        $list = $model->select('*')
            ->where('del',1)
            ->where('uniacid',$this->uniacid)
            ->orderBy('id','desc')
            ->paginate(10)
            ->toArray();

        return $this->apiSuccess('',[
            'list'=>$list['data'],
            'total'=>$list['total']
        ]);
    }

    public function edit(array $data){
        $model = new SubscribeMessage();
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
        $model = new SubscribeMessage();
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
