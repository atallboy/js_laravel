<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/8 16:00
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\master;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Master;
use Modules\Admin\Services\BaseService;

class MaterService extends BaseService
{
    public function getMasterPerformanceIdByMasterId($master_id){
        $q = DB::table('master_performance')
            ->where('master_id',$master_id)
            ->where('del',1)
            ->value('id');
        return $q;
    }


    public function index(array $data){

        $model = Master::query();
        $model = $this->queryCondition($model,$data,[],['name']);

        if($data['cate']==2){
            $list = $model->select('*')
                ->where('del',1)
                ->where('cate','>',1)
                ->where('uniacid',$this->uniacid)
                ->with(['user:*'])
                ->orderBy('id','desc')
                ->paginate(10)
                ->toArray();
        }
        else{
            $list = $model->select('*')
                ->where('del',1)
                ->where('cate',1)
                ->where('uniacid',$this->uniacid)
                ->with(['user:*'])
                ->orderBy('id','desc')
                ->paginate(10)
                ->toArray();
        }

        foreach ($list['data'] as $k=>$v){
            $list['data'][$k]['picArr'] = $v['pic']?explode('&',$v['pic']):[];
            $list['data'][$k]['idcardArr'] = $v['idcard']?explode('&',$v['idcard']):[];
            $list['data'][$k]['certificateArr'] = $v['certificate']?explode('&',$v['certificate']):[];
            $list['data'][$k]['pic'] = $list['data'][$k]['picArr']?$list['data'][$k]['picArr'][0]:'';
            if(!$v['user'])$list['data'][$k]['user'] = ['balance'=>0];

            $list['data'][$k]['master_service'] = [];
            $q = DB::table('master_service')
                ->join('item','master_service.item_id','=','item.id')
                ->where('master_id',$v['id'])
                ->where('master_service.del',1)
                ->where('item.del',1)
                ->where('item.status',1)
                ->select(['item.name','item.id'])
                ->get()->toArray();
            foreach ($q as $k1=>$v1){$list['data'][$k]['master_service'][] = $v1->name;}
        }

        return $this->apiSuccess('',[
            'list'=>$list['data'],
            'total'=>$list['total']
        ]);
    }

    public function edit(array $data){
        $master_service = '';
        if(array_key_exists('master_service',$data) && $data['master_service']){$master_service = $data['master_service'];unset($data['master_service']);}
        $model = new Master();
        $res = 'no';
        if(array_key_exists('id',$data)){
            $id = $data['id'];
            $cc = $data;
            unset($cc['id']);
            $res = $this->commonUpdate($model,$id,$cc);
        }else{

//            return $this->apiError('操作11失败！');
            $res = $this->commonCreate($model,$data);

            $inc = [
                'uniacid'=>$this->uniacid,
                'create_at'=>time(),
                'master_id'=>$res,
            ];
            $id = DB::table('master_performance')->insertGetId($inc);

        }

        DB::table('master_service')->where('master_id',$id)->where('del',1)->update(['del'=>0,'delete_at'=>time()]);
        $master_service = explode('&',$master_service);
        foreach ($master_service as $k=>$v){
            $q = DB::table('item')->where('name',$v)->where('del',1)->where('status',1)->first();
            if($q){
                $m = ['uniacid'=>$this->uniacid,'item_id'=>$q->id,'master_id'=>$id,'create_at'=>time()];
                DB::table('master_service')->insert($m);
            }

        }



//        $_id = $this->commonCreateOrrUpdate($model,$data);
//        DB::beginTransaction();
//        try{
//            DB::commit();
//        }catch(\Exception $e){
//            DB::rollBack();
//            return $this->apiError('操作失败！');
//        }
        return $this->apiSuccess($res,$data);
    }

    public function del(int $id){
        $model = new Master();
        DB::beginTransaction();
        try{
            $insert_id = $this->commonDel($model,$id);
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            $this->apiError('删除失败！');
        }
        return $this->apiSuccess('',[]);
    }
}
