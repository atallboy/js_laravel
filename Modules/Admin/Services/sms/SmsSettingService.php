<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/9/28 21:25
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\sms;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Role as model;
use Modules\Admin\Services\BaseService;

class SmsSettingService extends BaseService
{
    public function index(array $data){

        $model = model::query();
        $model = $this->queryCondition($model,$data,[]);
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
        $data['privilege'] = '&'.$data['privilege'].'&';
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
