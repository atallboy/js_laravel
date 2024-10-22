<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/19 15:13
 * @Description: 版权所有
 */

namespace Modules\Admin\Models;

class Order extends Base
{
    public $table = 'order';

    public function user(){
        return $this->hasOne('\Modules\Admin\Models\User','id','user_id');
    }

    public function master(){
        return $this->hasOne('\Modules\Admin\Models\Master','id','master_id');
    }

    public function address(){
        return $this->hasOne('\Modules\Admin\Models\Address','id','address_id');
    }

    public function order_product(){
        return $this->hasManyThrough('\Modules\Admin\Models\Item','\Modules\Admin\Models\OrderProduct','order_id','id','id','item_id');
    }

    public function paylog(){
        return $this->hasOne('\Modules\Admin\Models\Master','id','master_id');
    }

}
