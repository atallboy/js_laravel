<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/19 17:22
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\suggest;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Suggest;
use Modules\Admin\Services\BaseService;

class SuggestService extends BaseService
{
    public function index(array $data){

        $model = Suggest::query();
        $model = $this->queryCondition($model,$data,['nickName']);
        $list = $model->select('*')
            ->where('del',1)
            ->where('uniacid',$this->uniacid)
            ->orderBy('id','desc')
            ->paginate(10)
            ->toArray();
        foreach ($list['data'] as $k=>$v){
            $list['data'][$k]['pic_arr'] = explode('&',$v['pic']);
        }

        return $this->apiSuccess('',[
            'list'=>$list['data'],
            'total'=>$list['total']
        ]);
    }

    public function edit(array $data){
        $model = new Suggest();
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
        $model = new Suggest();
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
