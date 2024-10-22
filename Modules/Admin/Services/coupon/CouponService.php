<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/20 20:59
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\coupon;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Coupon;
use Modules\Admin\Services\BaseService;

class CouponService extends BaseService
{
    public function index(array $data){

        $model = Coupon::query();
        $model = $this->queryCondition($model,$data,['nickName']);
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
        $model = new Coupon();
        if($data['valid_time_type']==1){
//            $data['valid_start_time'] = strtotime($data['valid_start_time']);
            $data['valid_start_time'] = strtotime($data['valid_start_time']);
//            $data['valid_end_time'] = strtotime($data['valid_end_time']);
            $data['valid_end_time'] = strtotime($data['valid_end_time']);
        }else{
            $data['valid_start_time'] = 0;
            $data['valid_end_time'] = 0;
        }

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
        $model = new Coupon();
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
