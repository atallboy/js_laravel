<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/10/9 13:50
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\distribute;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\DistributeQrcodeBatch as Model;
use Modules\Admin\Services\BaseService;

class DistributeQrcodeBatchService extends BaseService
{
    public function index(array $data){

        $model = Model::query();
        $model = $this->queryCondition($model,$data,[]);
        $list = $model->select('*')
            ->where('del',1)
            ->where('uniacid',$this->uniacid)
//            ->with(['user:*'])
            ->orderBy('id','desc')
            ->paginate(10)
            ->toArray();


        return $this->apiSuccess('',[
            'list'=>$list['data'],
            'total'=>$list['total']
        ]);
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
