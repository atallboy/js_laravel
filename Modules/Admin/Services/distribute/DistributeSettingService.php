<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/8/23 22:45
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\distribute;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\DistributeSetting as model;
use Modules\Admin\Services\BaseService;

class DistributeSettingService extends BaseService
{
    public function index(array $data){

        $model = model::query();
        $model = $this->queryCondition($model,$data,[]);
        $data = $model->select('*')
            ->where('del',1)
            ->where('uniacid',$this->uniacid)
            ->orderBy('id','asc')
            ->first();

        return $this->apiSuccess('',$data);
    }

    public function edit(array $data){
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


}
