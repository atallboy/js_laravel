<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/9/30 22:30
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\city;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\City;
use Modules\Admin\Services\BaseService;

class CityService extends BaseService
{
    public function index(array $data){

        $model = City::query();
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

        $q = DB::table('city')->where('name',$data['name'])->where('del',1)->where('uniacid',$this->uniacid)->first();
        if($q){
            if(array_key_exists('id',$data) && $data['id']!=$q->id){
                return $this->apiError('该区域名称已存在');
            }else{
                return $this->apiError('该区域名称已存在');
            }
        }



        $model = new City();
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
        $model = new City();
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
