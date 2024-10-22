<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/9/15 10:18
 * @Description: 版权所有
 */

namespace Modules\Admin\Models;

class Eva extends Base
{
    public $table = 'eva_record';

    public function user(){
        return $this->hasOne('\Modules\Admin\Models\User','id','user_id');
    }

    public function order(){
        return $this->hasOne('\Modules\Admin\Models\Order','id','order_id');
    }

    public function master(){
        return $this->hasOne('\Modules\Admin\Models\Master','id','master_id');
    }

    public function item(){
        return $this->hasOne('\Modules\Admin\Models\Item','id','item_id');
    }
}
