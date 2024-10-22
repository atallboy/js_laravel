<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/8 15:59
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\item;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Item;
use Modules\Admin\Services\BaseService;

class ItemService extends BaseService
{
    public function index(array $data){

        $model = Item::query();
        $model = $this->queryCondition($model,$data,['status']);
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
        $model = new Item();
        DB::beginTransaction();
        try{
            $_id = $this->commonCreateOrrUpdate($model,$data);
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            return $this->apiError('操作失败！');
        }
        return $this->apiSuccess('',[]);
    }

    public function del(int $id){
        $model = new Item();
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
