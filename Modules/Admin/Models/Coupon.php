<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/20 20:58
 * @Description: 版权所有
 */

namespace Modules\Admin\Models;

class Coupon extends Base
{
    protected $table='coupon';

    public function getValidStartTimeAttribute($value){
        return $value?date('Y-m-d H:i:s',$value):'';
    }

    public function getValidEndTimeAttribute($value){
        return $value?date('Y-m-d H:i:s',$value):'';
    }

}
