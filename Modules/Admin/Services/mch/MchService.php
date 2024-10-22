<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/8/18 22:24
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\mch;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Mch;
use Modules\Admin\Services\BaseService;

class MchService extends BaseService
{
    public function index(array $data){

        $model = Mch::query();
        $model = $this->queryCondition($model,$data,[],['name']);

        $list = $model->select('*')
            ->where('del',1)
            ->where('uniacid',$this->uniacid)
//            ->with(['user:*'])
            ->orderBy('id','desc')
            ->paginate(10)
            ->toArray();

        foreach ($list['data'] as $k=>$v){
            $list['data'][$k]['picArr'] = explode('&',$v['pic']);
            $list['data'][$k]['pic'] = $list['data'][$k]['picArr'][0];
        }

        return $this->apiSuccess('',[
            'list'=>$list['data'],
            'total'=>$list['total']
        ]);
    }

    public function edit(array $data){
        $model = new Mch();
        DB::beginTransaction();
        try{
            $_id = $this->commonCreateOrrUpdate($model,$data);
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            return $this->apiError('操作失败！');
        }
        return $this->apiSuccess();
    }

    public function del(int $id){
        $model = new Mch();
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
