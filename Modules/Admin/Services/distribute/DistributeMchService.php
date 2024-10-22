<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/8/26 07:57
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\distribute;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\DistributeMch as Model;
use Modules\Admin\Services\BaseService;

class DistributeMchService extends BaseService
{
    public function getDistributeMchPerformanceIdByUserId($user_id){
        $q = DB::table('distribute_mch_performance')
            ->where('user_id',$user_id)
            ->where('del',1)
            ->value('id');
        if(!$q){
            DB::table('distribute_mch_performance')->insert(['user_id'=>$user_id,'uniacid'=>$this->uniacid,'create_at'=>time()]);
            $q = DB::table('distribute_mch_performance')
                ->where('user_id',$user_id)
                ->where('del',1)
                ->value('id');
        }
        return $q;
    }

    public function index(array $data){

        $model = Model::query();
        $model = $this->queryCondition($model,$data,['role']);
        $list = $model->select('*')
            ->where('del',1)
            ->where('status','<',2)
            ->where('uniacid',$this->uniacid)
            ->with(['user:*'])
            ->orderBy('id','desc')
            ->paginate(10)
            ->toArray();

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
