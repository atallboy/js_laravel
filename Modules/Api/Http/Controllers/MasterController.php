<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/9 22:58
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\master\MaterService;
use Modules\Common\Models\Doer;
use Modules\Common\Models\SubscribeMessage;
use Modules\Common\Models\Wechat;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MasterController extends ApiController
{


    //  public function jishiaddress(Request $request){

    //     echo '测试技师定位';


    //  }


   public function jishiaddress(Request $request){
        $id = $request->input('id');
        $inc = [
            'jindu'=>$request->input('jindu'),
            'weidu'=>$request->input('weidu'),
            'locationName' =>$request->input('locationName'),

            // 'jindu' => 344.34,
            // 'weidu' =>975.45,
        ];
        // $_id = DB::table('master')->where('id',$this->id)->insertGetId($this->cInc($inc));

        $affected = DB::table('master')
              ->where('id', $id)
              ->update($inc);
        return $this->apiSuccess('');
    }

    public function detail(Request $request){

        $id = $request->input('id');
        $master_id = $request->input('master_id');
        if($master_id)$id = $master_id;
        $data = DB::table('master')
            ->where('user_id',$this->user_id)
            ->where('del',1)
            ->where('cate',1)
            ->where('status','<',2)
            ->orderBy('id','asc')
            ->first();
        if($id){
            $data = DB::table('master')
                ->where('id',$id)
                ->where('del',1)
                ->orderBy('id','asc')
                ->first();
        }

        $data_performance = DB::table('master_performance')
            ->where('master_id',$id)
            ->where('del',1)
            ->orderBy('id','asc')
            ->first();

        $data->picArr = explode('&',$data->pic);
        $data->pic = $data->picArr[0];

        if((!$data->qrcode&&$this->agent_cate=='micro') || (!$data->qrcode_gzh&&$this->agent_cate=='gzh')){
            $wechat = new Wechat();
            $wechat->uniacid = $this->uniacid;
            $wechat->appid = $this->getsetting()->app_id;
            $wechat->appsecret = $this->getsetting()->app_secret;
            $access_token = $wechat->getGzhAccessToken();
            $up = ['update_at'=>time()];
            if($this->agent_cate=='gzh'){
                $param = ['action_name'=>'QR_LIMIT_SCENE'];
//                $up['qrcode_gzh'] = $qrcode = $wechat->getGzhQrcode($access_token,$param);
                $qrcode_name = 'create/qrcode/master/master_'.$data->id.'.png';
                $up['qrcode_gzh'] = $qrcode = $qrcode_name;
                $qrcode_url = $this->host.'/home/index?share_token='.$this->user->token.'&redirect_url='.urlencode($this->host.'/h5/index.html#/pages/master/list?master_id='.$data->id);
                QrCode::encoding ('UTF-8')
                        ->format('png')
                    ->errorCorrection('H')
                    ->size(2000)
//                    ->color(255, 0, 0)
//                    ->backgroundColor(255, 0, 0)
                    ->generate($qrcode_url,public_path($qrcode_name));
            }else{
                $param = (array("scene" => $data->id, "width" => 350, 'page' => 'pages/master/list', "auto_color" => true, "is_hyaline" => false));
                $up['qrcode'] = $qrcode = $wechat->getMicrQrcode($access_token,$param);
            }
            DB::table('master')->where('id',$data->id)->update($up);
            $data = DB::table('master')
                ->where('id',$data->id)
                ->where('del',1)
                ->orderBy('id','asc')
                ->first();
        }

        if($this->agent_cate=='gzh'){
            // $data->qrcode = 'http://'.$_SERVER['HTTP_HOST'].'/create/qrcode/master/'.$data->id.'.png';
            $data->qrcode =  'http://'.$_SERVER['HTTP_HOST'].'/'.$data->qrcode_gzh;
        }else{
            $data->qrcode = 'http://'.$_SERVER['HTTP_HOST'].$data->qrcode;
        }

        $order_all = DB::table('order')->where('master_id',$data->id)->where('status','!=',5)->where('uniacid',$this->uniacid)->where('del',1)->count();
        $order_wait_pay = DB::table('order')->where('status','=',0)->where('master_id',$data->id)->where('uniacid',$this->uniacid)->where('del',1)->count();
        $order_wait_start = DB::table('order')->where('status','=',1)->where('master_id',$data->id)->where('uniacid',$this->uniacid)->where('del',1)->count();
        $order_ing = DB::table('order')->where('status','>',20)->where('status','<',25)->where('master_id',$data->id)->where('uniacid',$this->uniacid)->where('del',1)->count();
        $wait_taking = DB::table('order')->where('status','=',20)->where('master_id',$data->id)->where('uniacid',$this->uniacid)->where('del',1)->count();
        $master_departure = DB::table('order')->where('status','=',21)->where('master_id',$data->id)->where('uniacid',$this->uniacid)->where('del',1)->count();
        $master_arrival = DB::table('order')->where('status','=',22)->where('master_id',$data->id)->where('uniacid',$this->uniacid)->where('del',1)->count();
        $master_start = DB::table('order')->where('status','=',23)->where('master_id',$data->id)->where('uniacid',$this->uniacid)->where('del',1)->count();
        $master_complete = DB::table('order')->where('status','=',24)->where('master_id',$data->id)->where('uniacid',$this->uniacid)->where('del',1)->count();

        $order_complete = DB::table('order')->where('status','=',3)->where('master_id',$data->id)->where('uniacid',$this->uniacid)->where('del',1)->count();
        $order_cancel = DB::table('order')->where('status','=',5)->where('master_id',$data->id)->where('uniacid',$this->uniacid)->where('del',1)->count();
        $order_apply_refund = DB::table('order')->where('status','=',11)->where('master_id',$data->id)->where('uniacid',$this->uniacid)->where('del',1)->count();
        $order_refund = DB::table('order')->where('status','=',12)->where('master_id',$data->id)->where('uniacid',$this->uniacid)->where('del',1)->count();

        $briefData =[
            'order_all'=>$order_all,
            'order_wait_start'=>$order_wait_start,
            'order_wait_pay'=>$order_wait_pay,
            'order_ing'=>$order_ing,
            'wait_taking'=>$wait_taking,
            'master_departure'=>$master_departure,
            'master_arrival'=>$master_arrival,
            'master_start'=>$master_start,
            'master_complete'=>$master_complete,
            'order_complete'=>$order_complete,
            'order_cancel'=>$order_cancel,
            'order_apply_refund'=>$order_apply_refund,
            'order_refund'=>$order_refund,
        ];
        $data->calc = $briefData;

        $data->certificateArr = explode('&',$data->certificate);

        $data->performance = $data_performance;

//        $data->eva_order = intval($data->base_review)+$data->eva_order;

         return $this->apiSuccess('',$data);
    }

    public function index(Request $request)
    {
//        echo "<pre>";print_r($access_token);echo "<pre>";die;

        $list = DB::table('master')
            ->join('user','master.user_id','=','user.id')
            ->join('master_performance','master.id','=','master_performance.master_id')
            ->where('master.uniacid',$this->uniacid)
            ->where('master.cate',1)
            ->where('master.status',1)
//            ->where('master.open_status',1)
            ->where('master.del',1)
            ->where('user.del',1)
            ->select(['master.*','user.avatarUrl'
                ,'master_performance.jiazhonglv','master_performance.complete_order','master_performance.jiazhong_order','master_performance.eva_order'
                ,'master_performance.review','master_performance.score','master_performance.collect']);

        if($name = $request->input('name',0)){
            $list->where('master.name','like','%'.$name.'%');
        }
        $city = $request->input('city',0);
        $city_type = DB::table('city')->where('name',$city)->where('del',1)->where('uniacid',$this->uniacid)->value('type');

        if($city && $city!='不限城市'){
            if($city_type==1)$list->where('master.province','like','%'.$city.'%');
            if($city_type==2)$list->where('master.city','like','%'.$city.'%');
            if($city_type==3)$list->where('master.district','like','%'.$city.'%');
//            $list->where('master.city','like','%'.$city.'%');
        }




        if($is_recommend = $request->input('is_recommend')){
            $list->where('master.is_recommend','=',$is_recommend);
        }
        if($is_hot = $request->input('is_hot')){
            $list->where('master.is_hot','=',$is_hot);
        }
        if($is_fast = $request->input('is_fast')){
            $list->where('master.is_fast','=',$is_fast);
        }
        if($travel_expense_free = $request->input('travel_expense_free')){
            $list->where('master.travel_expense','=',0);
        }
        if($is_jiazhonglv = $request->input('is_jiazhonglv')){
            $list->orderBy('master_performance.jiazhonglv','desc');
        }
        if($request->input('is_collect')){
            $list->join('master_collect','master.id','=','master_collect.master_id')
                ->where('master_collect.user_id','=',$this->user_id)
                ->where('master_collect.del','=',1)
                ->where('master_collect.master_id','>',0);
        }
        else{

        }

        $list = $list->get()->toArray();
        $doer = new Doer();
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        $arr = [];
        foreach ($list as $k=>$v){
            $list[$k]->picArr = explode('&',$v->pic);
            $list[$k]->pic = $list[$k]->picArr?$list[$k]->picArr[0]:'';
            if(!$v->jiazhonglv)$list[$k]->jiazhonglv = 0;
            $list[$k]->half_year_complete_order = DB::table('order')->where('master_id',$v->id)->where('status',4)->where('del',1)->count();
            $distance = $doer->distance($latitude,$longitude,$v->latitude,$v->longitude);
            $list[$k]->distance = $distance.'km';
            $list[$k]->quanzhong = $quanzhong = round($v->jiazhonglv*0.6+$this->distanceScore($distance)*0.4,2);
            $list[$k]->eva_order = intval($list[$k]->base_review)+$list[$k]->eva_order;

            $list[$k]->complete_order = $v->complete_order+$v->base_order;
            $list[$k]->collect = $v->collect+$v->base_collect;
            $list[$k]->review = $v->review+$v->base_review;

            $q = DB::table('master_collect')->where('user_id',$this->user_id)->where('master_id',$v->id)->where('del',1)->first();
            $q?$list[$k]->is_collected=1:$list[$k]->is_collected=0;

            foreach ($list[$k] as $k1=>$v1){
                $arr[$k][$k1] = $v1;
            }

        }

//        $qzArr = [];
//        $distanceArr = [];
//        foreach ($arr as $key => $row)
//        {
//            $qzArr[$key] = $row['quanzhong'];
//            $distanceArr[$key]  = $row['distance'];
//        }

        array_multisort(array_column($arr, 'quanzhong'), SORT_DESC, $arr);

        $c1 = [];
        $c2 = [];
        $c3 = [];
        $item_id = $request->input('item_id');
        $today_start_time = strtotime(date('Y/m/d'));
        $time = intval(date('H'));
        $next_time = $time+1;

        foreach ($arr as $k=>$v){
            if($item_id){
                $q = DB::table('master_service')->where('item_id',$item_id)->where('master_id',$v['id'])->where('del',1)->first();
                if(!$q)continue ;
            }


            if($v['open_status']==1){
                $time_status = DB::table('order')
                    ->where('master_id',$v['id'])
                    ->where('service_time',$time.'-'.$next_time.'点')
                    ->where('status','>',0)
                    ->where('status','<',6)
                    ->where('create_at','>=',$today_start_time)
                    ->where('create_at','<',$today_start_time+86399)
                    ->where('del',1)
                    ->first();
                $v['service_status']=0;
                if($time_status){
                    $v['service_status']=1;
                    $c3[] = $v;
                }else{
                    $c1[] = $v;
                }


            }else{
                $c2[] = $v;
            }
        }

        $arr = array_merge($c1,$c3,$c2);




//        echo "<pre>";print_r($arr);echo "<pre>";die;
//
//        array_multisort($arr, SORT_ASC, $qzArr,SORT_ASC,$qzArr);

        return $this->apiSuccess($today_start_time,$arr);
    }

    public function distanceScore($distance){
        $score = 0;
        switch (ceil($distance)){
            case 0: $score=100;break ;
            case 1: $score=100;break ;
            case 2: $score=80;break ;
            case 3: $score=60;break ;
            case 4: $score=40;break ;
            case 5: $score=20;break ;
            default:$score=0;
        }
        return $score;
    }

    public function changeOpenStatus(Request $request){
        $res = DB::table('master')->where('id',$this->master_id)->update(['open_status'=>$this->master->open_status==1?0:1,'update_at'=>time()]);
        return $this->apiSuccess('',$res);
    }

    public function edit(Request $request){
        $id = $request->input('id',0);
        if(!$id&&$this->master_id)return $this->apiError('你已入驻，无法再次提交');
        $master = DB::table('master')->where('id',$id)->first();
        $inc = [
            'name'=>$request->input('name'),
            'tel'=>$request->input('tel'),
            'age'=>$request->input('age'),
            'gender'=>$request->input('gender'),
            'province'=>$request->input('province'),
            'city'=>$request->input('city'),
            'district'=>$request->input('district'),
            'address'=>$request->input('address'),
            'latitude'=>$request->input('latitude'),
            'longitude'=>$request->input('longitude'),
            'pic'=>$request->input('pic'),
            'idcard'=>$request->input('idcard'),
            'certificate'=>$request->input('certificate'),
            'travel_expense'=>$request->input('travel_expense',0),
            'bus_fee'=>$request->input('bus_fee'),
            'taxi_fee'=>$request->input('taxi_fee'),
            'store_name'=>$request->input('store_name'),
            'desc'=>$request->input('desc'),
            'qibujia'=>$request->input('qibujia',0),
            'update_at'=>time()
        ];


        if($master){
            $inc['open_status']=$request->input('open_status');
            $inc['auto_taking']=$request->input('auto_taking');
            $id = $master->id;
            DB::table('master')->where('id',$id)->update($inc);
        }else{
            $inc['uniacid']=$this->uniacid;
            $inc['status']=0;
            $inc['open_status']=0;
            $inc['cate'] = $request->input('cate',1);
            $inc = $this->cInc($inc);
            $id = DB::table('master')->insertGetId($inc);

            $inc = [
                'uniacid'=>$this->uniacid,
                'create_at'=>time(),
                'master_id'=>$id,
            ];
            DB::table('master_performance')->insert($inc);
        }

        DB::table('master_service')->where('master_id',$id)->where('del',1)->update(['del'=>0,'delete_at'=>time()]);
        $master_service = $request->input('master_service');
        if($master_service){
            $item_arr = explode('&',$master_service);
            foreach ($item_arr as $k=>$v){
                $m = ['uniacid'=>$this->uniacid,'item_id'=>$v,'master_id'=>$id,'create_at'=>time()];
                DB::table('master_service')->insert($m);
            }
        }

        return $this->apiSuccess('',$id);
    }

    public function collect(Request $request){
        $master_id = $request->input('id');
        $q = DB::table('master_collect')->where('user_id',$this->user_id)->where('master_id',$master_id)->where('del',1)->first();
        $master_performance_id = (new MaterService())->getMasterPerformanceIdByMasterId($master_id);
        if($q){
            DB::table('master_collect')->where('id',$q->id)->update(['del'=>0,'delete_at'=>time()]);
            DB::table('master_performance')->where('id',$master_performance_id)->decrement('collect');
        }else{
            DB::table('master_collect')->insert($this->cInc(['master_id'=>$master_id]));
            DB::table('master_performance')->where('id',$master_performance_id)->increment('collect');
        }
        return $this->apiSuccess('',$master_id);
    }


}
