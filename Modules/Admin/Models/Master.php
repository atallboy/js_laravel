<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/8 15:59
 * @Description: 版权所有
 */

namespace Modules\Admin\Models;

class Master extends Base
{
    protected $table='master';

//    public function getPicAttribute($pic){
//        return strpos($pic,$_SERVER['HTTP_HOST'])?$pic:'http://'.$_SERVER['HTTP_HOST'].'/upload/'.$pic;
//    }

    public function getCompleteOrderAttribute($data){
        return $data?$data:0;
    }

    public function user(){
        return $this->hasOne('\Modules\Admin\Models\User','id','user_id');
    }


}
