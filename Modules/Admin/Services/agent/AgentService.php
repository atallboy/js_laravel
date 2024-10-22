<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/22 17:15
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\agent;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Agent;
use Modules\Admin\Services\BaseService;

class AgentService extends BaseService
{
    public function index(array $data){

        $model = Agent::query();
        $model = $this->queryCondition($model,$data,[]);
        $list = $model->select('*')
            ->where('del',1)
            ->where('user_id','>',0)
            ->where('uniacid',$this->uniacid)
            ->with(['user' => function ($query) {
                $query->where('del', 1);
            }])
            ->orderBy('id','desc')
            ->paginate(10)
            ->toArray();

        return $this->apiSuccess('',[
            'list'=>$list['data'],
            'total'=>$list['total']
        ]);
    }

    public function edit(array $data){
        $model = new Agent();
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
        $model = new Agent();
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
