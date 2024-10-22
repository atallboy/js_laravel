<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/22 17:15
 * @Description: 版权所有
 */

namespace Modules\Admin\Models;

class Agent extends Base
{
    protected $table='agent';

    public function user(){
        return $this->hasOne('\Modules\Admin\Models\User','id','user_id');
    }
}
