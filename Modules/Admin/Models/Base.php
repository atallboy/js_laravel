<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/8 15:06
 * @Description: 版权所有
 */

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
class Base extends Model
{
    public $timestamps = false;
    public function getDateFormat() {
        return 'U';
    }
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getCreateAtAttribute()
    {
        return date('Y-m-d H:i', $this->attributes['create_at']);
    }

    public function getUpdateAtAttribute()
    {
        return $this->attributes['update_at']?date('Y-m-d H:i', $this->attributes['update_at']):'';
    }
    public function getRecentAtAttribute()
    {
        return $this->attributes['recent_at']?date('Y-m-d H:i', $this->attributes['recent_at']):'';
    }

    public function getGetTelTimeAttribute()
    {
        return $this->attributes['get_tel_time']?date('Y-m-d H:i', $this->attributes['get_tel_time']):'';
    }
}
