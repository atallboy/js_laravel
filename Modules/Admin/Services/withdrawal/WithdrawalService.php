<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/26 14:22
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\withdrawal;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\WithdrawalRecord;
use Modules\Admin\Services\balance\BalanceRecord;
use Modules\Admin\Services\BaseService;

class WithdrawalService extends BaseService
{
    public function index(array $data){

        $model = WithdrawalRecord::query();
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

    public function edit(array $data,$id){
        $model = new WithdrawalRecord();
        DB::beginTransaction();
        try{
            if($data['status']==1){
                DB::table('user')->where('id',$data['user_id'])->increment('withdrawal',$data['money']);
            }
            if($data['status']==2){
                BalanceRecord::balanceChange($data['user_id'],$data['money'],11,1,$id);
            }
            $_id = $this->commonUpdate($model,$id,$data);
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            return $this->apiError('操作失败！');
        }
        return $this->apiSuccess('',[]);
    }

    public function del(int $id){
        $model = new WithdrawalRecord();
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
