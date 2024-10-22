<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/20 14:40
 * @Description: 版权所有
 */

namespace Modules\Common\Models;

use Illuminate\Support\Facades\DB;

class Common
{
    public static function getNavigateParam($data=[]){
        $arr = [
            ['type'=>0,'msg'=>'无跳转','url'=>'','go_type'=>''],
            ['type'=>1,'msg'=>'技师加盟','url'=>'/pages/master/register?cate=1','go_type'=>1],
            ['type'=>2,'msg'=>'理疗师列表','url'=>'/pages/master/list','go_type'=>2],
            ['type'=>101,'msg'=>'主播招聘','url'=>'/pages/master/zhubo?cate=101','go_type'=>1],
            ['type'=>102,'msg'=>'达人招募','url'=>'/pages/master/daren?cate=102','go_type'=>1],
        ];
        if($data){
            foreach ($arr as $k=>$v){
                if($v['type']==$data->url)return $v;
            }
        }else{
            return $arr;
        }
    }

    public function incrementValue($table,$id,$field,$value=1){
        $v = DB::table($table)->where('id',$id)->value($field);
        $res = DB::table($table)->where('id', $id)->update([$field=>$v+$value]);
        return $res;
    }
}
