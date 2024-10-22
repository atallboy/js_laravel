<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/6/11 11:07
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\user;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Modules\Admin\Models\User;
use Modules\Admin\Services\BaseService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UserService extends BaseService
{
    public function index(array $data){

        $model = User::query();
        $model = $this->queryCondition($model,$data,[],['nickName','tel']);

        $model = $model->select('*')
            ->where('del',1)
            ->where('uniacid',$this->uniacid)
            ->orderBy('id','desc');

        if(array_key_exists('getcate',$data) && $data['getcate']=='distribute'){
            $model = $model->where('pre_id','>',0);
        }

        if(array_key_exists('only_tel',$data) && $data['only_tel']==1){
            $model = $model->where('tel','>',0);
        }

        $list = $model
            ->paginate(10)
            ->toArray();



        foreach ($list['data'] as $k=>$v){
            if($v['qrcode_gzh'])$list['data'][$k]['qrcode_gzh'] = 'https://'.$_SERVER['HTTP_HOST'].'/'.$v['qrcode_gzh'].'?r='.rand(10000,99999);
            if($v['pre_id']){
                $list['data'][$k]['pre_user'] = DB::table('user')->where('id',$v['pre_id'])->first();
            }
        }

        return $this->apiSuccess('',[
            'list'=>$list['data'],
            'total'=>$list['total']
        ]);
    }

    public function del(int $id){
        $model = new User();
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

    public function createQrcode1($user_id,$host){
        $user = DB::table('user')->where('id',$user_id)->first();
        $qrcode_name = '/create/qrcode/user/user_'.$user_id.'.png';

        $qrcode_url = $host.'/home/index?i='.$this->uniacid.'&share_token='.$user->token.'&redirect_url='.urlencode($host.'/h5/index.html#/pages/index/index');
        QrCode::encoding ('UTF-8')
            ->format('png')
            ->errorCorrection('H')
            ->size(300)
//                    ->color(255, 0, 0)
//                    ->backgroundColor(255, 0, 0)
            ->generate($qrcode_url,public_path($qrcode_name));
        DB::table('user')->where('id',$user->id)->update(['qrcode_gzh'=>$qrcode_name,'update_at'=>time()]);
        return $qrcode_name;
    }

    public function createQrcode($user_id,$host){
        $user = DB::table('user')->where('id',$user_id)->first();

        $distribute_mch = DB::table('distribute_mch')->where('uniacid',$this->uniacid)->where('user_id',$user_id)->where('del',1)->orderBy('id','desc')->first();
        if(!$distribute_mch || ($distribute_mch&& $distribute_mch->status!=1))return $this->apiError('该用户不具备分销商资格，无权限生成分销二维码！');

        $qrcode = $this->getLocalQrcode($user,$host);
        $avatar = $this->getLocalAvatar($user);
        $setting = DB::table('setting_distribute')->where('uniacid',$this->uniacid)->where('del',1)->first();
        $param = [
            'qrcode'=>$qrcode,
            'avatar'=>$avatar,
            'text'=>$user->nickName,
            'background_pic'=>$setting->background_pic,
            'position_x'=>$setting->position_x,
            'position_y'=>$setting->position_y,
            'position_x_name'=>$setting->position_x_name,
            'position_y_name'=>$setting->position_y_name,
            'position_x_avatar'=>$setting->position_x_avatar,
            'position_y_avatar'=>$setting->position_y_avatar,
            'save_path'=>'create/qrcode/user/'.md5(rand(10000,99999).time()).'.png'
        ];
//            return $this->apiSuccess('',$param);
        $path = $this->mergeImages($param);

        DB::table('user')->where('id',$user->id)->update(['qrcode_gzh'=>$param['save_path'],'update_at'=>time()]);
        return $this->apiSuccess('',$host.'/'.$param['save_path']);
    }

    public function getLocalQrcode($user,$host){
        $src = 'create/qrcode/user/'.$user->id.'.png';
        $pic = public_path($src);
        if(!file_exists($pic)){
            $qrcode_url = $host.'/home/index?i='.$user->uniacid.'&share_token='.$user->token.'&redirect_url='.urlencode($host.'/h5/index.html#/pages/index/index');
            QrCode::encoding ('UTF-8')
                ->format('png')
                ->errorCorrection('H')
                ->size(300)
//                    ->color(255, 0, 0)
//                    ->backgroundColor(255, 0, 0)
                ->generate($qrcode_url,public_path($src));
        }
        return $pic;
    }

    public function getLocalAvatar($user){
        $src = 'create/qrcode/user/'.md5(base64_encode($user->id)).'.png';
        $pic = public_path($src);
        if(!file_exists($pic)){
            $result = $this->downloadImage($user->avatarUrl, $pic);
            if(!$result)return ['code'=>0,'message'=>'微信图像缓存至本地失败'];
        }
        return $pic;
    }

    public function mergeImages($param)
    {

        // 加载背景图片
        $background = Image::make($param['background_pic']);

        // 加载二维码图片  200x200
        $qrcode = Image::make($param['qrcode']);
        // 计算二维码合成的位置 (例如: 左侧30%，顶部50%)
        $x = round($background->width() * $param['position_x']/100) ;
        $y = round($background->height() * $param['position_y']/100);
        // 将二维码合成到背景图上
        $background->insert($qrcode, 'top-left', $x, $y);

        //头像  132x132
        if(array_key_exists('avatar',$param)){
            $qrcode = Image::make($param['avatar']);
            // 计算头像合成的位置 (例如: 左侧30%，顶部50%)
            $x = round($background->width() * $param['position_x_avatar']/100) ;
            $y = round($background->height() * $param['position_y_avatar']/100);
            // 将头像合成到背景图上
            $background->insert($qrcode, 'top-left', $x, $y);
        }

        // 添加文字到图片 (例如: 左侧30%，顶部60%)
        if(array_key_exists('text',$param)){
            $textX = round($background->width() * $param['position_x_name']/100);
            $textY = round($background->height() * $param['position_y_name']/100);
            $background->text($param['text'], $textX, $textY, function($font) {
                $font->file(public_path('static/font/simsun.ttc')); // 字体文件路径
                $font->size(50); // 字体大小
                $font->color('#000000'); // 字体颜色
                $font->align('center'); // 水平对齐
                $font->valign('middle'); // 垂直对齐
            });
        }


        // 保存最终合成的图片
        $path = public_path($param['save_path']);
        $background->save($path);

        return $param['save_path'];
    }

    function downloadImage($url, $savePath)
    {
        $file = fopen($savePath, 'wb');
        $options = [
            'http' => [
                'method'  => 'GET',
                'header'  => "Accept-language: en\r\n" .
                    "Cookie: foo=bar\r\n"
            ]
        ];
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result !== FALSE) {
            file_put_contents($savePath, $result);
            fclose($file);
            return true;
        } else {
            return false;
        }
    }

    public function createDistributeQrcode($data){
        $dir = 'create/qrcode/distribute';
        if(!file_exists(public_path($dir))){
            if (!mkdir(public_path($dir), 0777, true)) {
                return 'Failed to create directories';
            }
        }
        $src = 'create/qrcode/distribute/'.$data['file_name'].'.png';
        $qrCodeNumber = "二维码编号：".$data['serial_number'];
        $pic = public_path($src);
        if(!file_exists($pic)){

            $qrcode_url = $data['host'].'/home/index?act=hotel_distribute&i='.$data['uniacid'].'&share_token='.$data['serial_number'].'&redirect_url='.urlencode($data['host'].'/h5/index.html#/pages/index/index');

            $qrcode = QrCode::encoding('UTF-8')
                ->format('png')
                ->errorCorrection('H')
                ->size(300)
                ->margin(4) // 设置二维码的留白边框
                ->generate($qrcode_url);

            $tempImage = public_path('temp_qrcode.png');
            file_put_contents($tempImage, $qrcode);

            $image = Image::make($tempImage)
                ->resize(300, 300) // 确保二维码大小一致
                ->resizeCanvas(350, 370, 'top', false, 'ffffff') // 在四周添加留白，底部额外留出空间
                ->text($qrCodeNumber, 175, 360, function($font) {
//                    $font->file('/www/wwwroot/jsdjys/public/static/font/simsun.ttc');
                    $font->file(public_path('static/font/simsun.ttc'));
//                    $font->file(public_path('static/font/STHUPO.TTF'));
                    $font->size(22);
                    $font->color('#000000'); // 文字颜色
                    $font->align('center');
                });

            $image->save(public_path($src));
            @unlink($tempImage);


        }


        return '/'.$src;
    }

}
