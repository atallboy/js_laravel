<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/8 15:05
 * @Description: 版权所有
 */

namespace Modules\Admin\Services;


class CommonService
{
    public $uniacid=1;

    public function apiSuccess(string $message = '操作成功',$data = array(),int $status = 20000){
        return response()->json([
            'code' => $status,
            'message'=> $message,
            'data'=>$data
        ],200);
    }

    public function apiError(string $message = '失败',$data = array(),int $status = 40000){
        return response()->json([
            'code' => $status,
            'message'=> $message,
            'data'=>$data
        ],200);
    }

    public function validateErrorMsg($validator){
        return array_values($validator->errors()->messages())[0][0];
    }

    function queryCondition(object $model,array $params,array $condition=[],array $blurCondition=[]):Object
    {
        if (!empty($params['created_at'])){
            $model = $model->whereBetween('created_at',$params['created_at']);
        }
        if (!empty($params['updated_at'])){
            $model = $model->whereBetween('updated_at',$params['updated_at']);
        }
//        if (!empty($params[$key])){
//            $model = $model->where($key,'like','%' . $params[$key] . '%');
//        }
        if (isset($params['status']) && $params['status'] != ''){
            $model = $model->where('status',$params['status']);
        }
        foreach ($condition as $k=>$v){
            if ( array_key_exists($v,$params) && !empty($params[$v])){
                $model = $model->where($v,$params[$v]);
            }
        }
        foreach ($blurCondition as $k=>$v){
            if (array_key_exists($v,$params) && !empty($params[$v])){
                $model = $model->where($v,'like','%' . $params[$v] . '%');
            }
        }

//        echo "<pre>";print_r($condition);echo "<pre>";die;
        return $model;
    }

    public function commonCreate($model,array $data = [],$is_uniacid=true){
        if($is_uniacid)$data['uniacid'] = $this->uniacid;
        $data['create_at'] = time();
        $data['update_at'] = time();
        if(array_key_exists('id',$data))unset($data['id']);
        if ($insert_id = $model->insertGetId($data)){
            return $insert_id;
        }
        return false;
    }

    public function commonUpdate($model,$id,array $data = []){
        $data['update_at'] = time();
        if ($model->where('id',$id)->update($data)){
            return $id;
        }
        return false;
    }

    public function commonCreateOrrUpdate($model,array $data = [],$is_uniacid=true){
        if($is_uniacid)$data['uniacid'] = $this->uniacid;

        $_type = 1;
        $id = 0;
        if($_type==1){
            if(array_key_exists('id',$data)){
                $id = $data['id'];
                unset($data['id']);
            }
        }
        else{
            foreach ($data as $k=>$v){
                $model = $model->where($k,$v);
            }
            $query = $model->first();
            $id = $query->id;
        }

        $data['update_at'] = time();
        if($id){
            if ($model->where('id',$id)->update($data)){
                return $id;
            }
            return false;
        }
        else{
            $data['create_at'] = time();
            if ($insert_id = $model->insertGetId($data)){
                return $id.'-'.$insert_id;
            }
            return false;
        }
    }

    public function commonDel($model,$id){
        $data['del'] = 0;
        $data['delete_at'] = time();
        if ($model->where('id',$id)->update($data)){
            return $id;
        }
        return false;
    }

}

