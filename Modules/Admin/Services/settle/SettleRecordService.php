<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/8/3 12:25
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\settle;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\SettleRecord;
use Modules\Admin\Services\BaseService;

class SettleRecordService extends BaseService
{
    public function index(array $data){

        $model = SettleRecord::query();
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
}
