<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/8/7 07:45
 * @Description: 版权所有
 */

namespace Modules\Admin\Models;

class Admin extends Base
{
    protected $table='admin';

    public function role(){
        return $this->hasOne('\Modules\Admin\Models\Role','id','role_id')->withDefault();
    }

}

