<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/9/15 10:18
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\eva;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Eva as Model;
use Modules\Admin\Services\BaseService;

class EvaService extends BaseService
{
    public function index(array $data){

        $model = Model::query();
        $model = $this->queryCondition($model,$data);

        if (array_key_exists('nickName',$data) &&  !empty($data['nickName'])) {
            $nickName = $data['nickName'];
            $model->whereHas('user', function ($query) use ($nickName) {
                $query->where('nickName', $nickName);
            });
        }

        if (array_key_exists('master_name',$data) &&  !empty($data['master_name'])) {
            $master_name = $data['master_name'];
            $model->whereHas('master', function ($query) use ($master_name) {
                $query->where('name', $master_name);
            });
        }

        if (array_key_exists('item_name',$data) &&  !empty($data['item_name'])) {
            $item_name = $data['item_name'];
            $model->whereHas('item', function ($query) use ($item_name) {
                $query->where('name', $item_name);
            });
        }


        $list = $model->select('*')
            ->where('del',1)
            ->where('uniacid',$this->uniacid)
            ->with(['user:*'])
            ->with(['order:*'])
            ->with(['master:*'])
            ->with(['item:*'])
            ->orderBy('id','desc')
            ->paginate(10)
            ->toArray();

        foreach ($list['data'] as $k=>$v){
            $list['data'][$k]['tagArr'] = explode('&',$v['tag']);
        }

        return $this->apiSuccess('',[
            'list'=>$list['data'],
            'total'=>$list['total']
        ]);
    }

    public function edit(array $data){
        $model = new Model();
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
