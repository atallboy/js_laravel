<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/8/26 07:56
 * @Description: 版权所有
 */

namespace Modules\Admin\Models;

class DistributeMch extends Base
{
    protected $table='distribute_mch';

    public function user(){
        return $this->hasOne('\Modules\Admin\Models\User','id','user_id');
    }

}
