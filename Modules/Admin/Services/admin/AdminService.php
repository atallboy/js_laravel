<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/8/7 12:43
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\admin;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Admin as model;
use Modules\Admin\Services\BaseService;
use Modules\Common\Models\Doer;

class AdminService extends BaseService
{
    public function index(array $data){

        $model = model::query();
        $model = $this->queryCondition($model,$data,[]);
        $list = $model->select('*')
            ->where('del',1)
            ->with(['role' => function ($query) {
                $query->select('*')->where('del', 1);
            }])
            ->where('uniacid',$this->uniacid)
            ->orderBy('id','desc')
            ->paginate(10)
            ->toArray();

        foreach ($list['data'] as $k=>$v){
            $list['data'][$k]['password'] = '';
        }

        return $this->apiSuccess('',[
            'list'=>$list['data'],
            'total'=>$list['total']
        ]);
    }

    public function edit(array $data){
        if(strlen($data['account'])<5)return $this->apiError('账号不能少于5位数！');


        $query = DB::table('admin')
            ->where('uniacid',$this->uniacid)
            ->where('account',$data['account'])
            ->where('del',1)
            ->first();
        if($query && (!array_key_exists('id',$data)|| (array_key_exists('id',$data)&&!$data['id'])))return $this->apiError('该账号已存在，请更换！');

        if((!array_key_exists('id',$data) || (array_key_exists('id',$data)&&$data['password']))){
            if(strlen($data['password'])<6)return $this->apiError('密码不能少于6位数！');
            $data['password'] = $this->getPassword($data['password']);
        }else{
            unset($data['password']);
        }

        if((!array_key_exists('id',$data)|| (array_key_exists('id',$data)&&!$data['id']))) {
            $data['token'] = Doer::createToken();
        }

        $model = new model();
        $_id = $this->commonCreateOrrUpdate($model,$data);
        DB::beginTransaction();
        try{
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            return $this->apiError('操作失败！');
        }
        return $this->apiSuccess('',[]);
    }

    public function del(int $id){
        $model = new model();
        DB::beginTransaction();
        try{
            $insert_id = $this->commonDel($model,$id);
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            return $this->apiError('删除失败！');
        }
        return $this->apiSuccess('',[]);
    }



    public function getPassword($str){
        $password = md5($str.env('ADMIN_PASSWORD_SALT'));
        return $password;
    }

    public function getAdminInfo(){

        $session = session(env('ADMIN_TOKEN_NAME'));
        $admin = DB::table('admin')->where('token',$session)->where('del',1)->first();
        $data = ['privilege'=>[],'name'=>'','avatarUrl'=>'','account'=>'','admin_id'=>'9999','privilege_cate'=>0];
        if($admin){
            $role = DB::table('role')->where('id',$admin->role_id)->where('del',1)->first();

            $data = [
                'admin_id'=>$admin->id,
                'admin'=>$admin,
                'name'=>$admin->name,
                'account'=>$admin->account,
                'privilege_cate'=>$admin->privilege_cate,
                'avatarUrl'=>$admin->avatarUrl,
                'token'=>$session,
                'privilege'=> $role?explode('&',trim($role->privilege, "&")):[],
                'privilege_str'=> $role?$role->privilege:[],
            ];
        }
//        echo "<pre>";print_r($data);echo "<pre>";die;

        $data['isLogin'] = $session?true:false;

        return $data;
    }


}
