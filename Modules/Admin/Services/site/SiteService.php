<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/8/22 18:30
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\site;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Site;
use Modules\Admin\Services\BaseService;

class SiteService extends BaseService
{
    public function index(array $data){

        $model = Site::query();
        $model = $this->queryCondition($model,$data,[]);
        $list = $model->select('*')
            ->where('del',1)
            ->orderBy('id','desc')
            ->paginate(10)
            ->toArray();

        return $this->apiSuccess('',[
            'list'=>$list['data'],
            'total'=>$list['total']
        ]);
    }

    public function edit(array $data){
        $model = new Site();
        $_id = $this->commonCreateOrrUpdate($model,$data,false);
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
        $model = new Site();
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
