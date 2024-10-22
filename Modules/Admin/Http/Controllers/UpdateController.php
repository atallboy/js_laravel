<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/8/5 17:56
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Admin\Services\log\LogServices;

class UpdateController extends BaseController
{

    public function updateVersion(){

        $log = new LogServices();

        $is_update = false;

        //2.0.1
        $field_exists = DB::select(DB::raw("SHOW COLUMNS FROM jishi_lincec_admin WHERE Field = 'name'"));
        if (empty($field_exists)) {
            DB::statement('ALTER TABLE jishi_lincec_admin add COLUMN name varchar(30)');
        }


        $exists = DB::select(DB::raw("SHOW TABLES LIKE 'jishi_lincec_role'"));
        if (count($exists) == 0) {

            if (!Schema::hasColumn('jishi_lincec_log', 'cate')) {
                DB::statement('ALTER TABLE jishi_lincec_log MODIFY COLUMN cate int default 0');
            }

            $role = '
                CREATE TABLE IF NOT EXISTS `jishi_lincec_role`(
                    `id` int(6) unsigned not null AUTO_INCREMENT,
                    `uniacid` int,
                    `name` varchar(32),
                    `desc` varchar(1000),
                    `privilege` varchar(2000),
                    `status` tinyint default 0,
                    `del` tinyint default 1,
                    `create_at` int,
                    `update_at` int,
                    `delete_at` int,
                    key(`uniacid`),
                    primary key(`id`)
                )engine=InnoDB AUTO_INCREMENT=1 DEFAULT charset=utf8mb4 comment="角色表"
            ';
            DB::statement($role);

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = '2.0.1';
            $log->content = '新增角色权限管理模型、管理员账户模块、新增一键更新数据库功能、修改log表cate字段长度、修改admin表新增name字段';
            $log->saveLog();

            $is_update = true;

        }


//        if (!Schema::hasColumn('jishi_lincec_log', 'cate')) {
//            DB::statement('ALTER TABLE jishi_lincec_log ADD COLUMN gender2 VARCHAR(255) NULL');
//        }


        //2.0.2
        $field_exists = DB::select(DB::raw("SHOW COLUMNS FROM jishi_lincec_admin WHERE Field = 'avatarUrl'"));
        if (empty($field_exists)) {
            DB::statement('ALTER TABLE jishi_lincec_admin add COLUMN avatarUrl varchar(300)');
            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = '2.0.2';
            $log->content = '新增管理员头像编辑功能';
            $log->saveLog();
            $is_update = true;
        }

        //2.0.3
        $field_exists = DB::select(DB::raw("SHOW COLUMNS FROM jishi_lincec_order_refund WHERE Field = 'type'"));
        if (empty($field_exists)) {
            DB::statement('ALTER TABLE jishi_lincec_order_refund add COLUMN type int');
        }

        $field_exists = DB::select(DB::raw("SHOW COLUMNS FROM jishi_lincec_order_refund WHERE Field = 'check_status'"));
        if (empty($field_exists)) {
            DB::statement('ALTER TABLE jishi_lincec_order_refund add COLUMN check_status int');
        }

        $field_exists = DB::select(DB::raw("SHOW COLUMNS FROM jishi_lincec_order_refund WHERE Field = 'apply_fee'"));
        if (empty($field_exists)) {
            DB::statement('ALTER TABLE jishi_lincec_order_refund add COLUMN apply_fee decimal(10,2) default 0');
        }

        $field_exists = DB::select(DB::raw("SHOW COLUMNS FROM jishi_lincec_setting WHERE Field = 'refund_destination'"));
        if (empty($field_exists)) {
            DB::statement('ALTER TABLE jishi_lincec_setting add COLUMN refund_destination tinyint default 1 comment "1统一到账户余额2原路返回"');
        }

        $field_exists = DB::select(DB::raw("SHOW COLUMNS FROM jishi_lincec_order_refund WHERE Field = 'order_status'"));
        if (empty($field_exists)) {
            DB::statement('ALTER TABLE jishi_lincec_order_refund add COLUMN order_status int');
            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = '2.0.3';
            $log->content = '新增订单状态记录功能、升级退款流程，可配置退款去向';
            $log->saveLog();
            $is_update = true;
        }

        //查询是否存在 157 2.0.4版本
        $q = DB::table('log')->where('cate',157)->where('remark','2.0.4')->where('uniacid',$this->uniacid)->first();
        if (!$q) {
            DB::statement('ALTER TABLE jishi_lincec_master MODIFY COLUMN pic VARCHAR(3000);');
            DB::statement('ALTER TABLE jishi_lincec_master MODIFY COLUMN certificate VARCHAR(3000);');
            DB::statement('ALTER TABLE jishi_lincec_master add COLUMN idcard VARCHAR(3000);');

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = '2.0.4';
            $log->content = '师傅信息新增身份证、资格证书编辑项目，并可上传多张图片';
            $log->saveLog();
            $is_update = true;
        }

        //2.0.5版本
        $q = DB::table('log')->where('cate',157)->where('remark','2.0.5')->where('uniacid',$this->uniacid)->first();
        if (!$q) {
            DB::statement('ALTER TABLE jishi_lincec_setting add COLUMN auto_receipt tinyint default 7;');
            DB::statement('ALTER TABLE jishi_lincec_order add COLUMN start_service_time int;');
            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = '2.0.5';
            $log->content = '新增订单自动签收功能，';
            $log->saveLog();
            $is_update = true;
        }

        //2.0.6版本
        $q = DB::table('log')->where('cate',157)->where('remark','2.0.6')->where('uniacid',$this->uniacid)->first();
        if (!$q) {

            $table = '
                CREATE TABLE IF NOT EXISTS `jishi_lincec_setting_distribute`(
                    `id` int(6) unsigned not null AUTO_INCREMENT,
                    `uniacid` int,
                    `mch_need_check` tinyint default 1 comment "0不需要1需要",
                    `status` tinyint default 0,
                    `level` tinyint default 1,
                    `percent_first` TINYINT default 0,
                    `percent_second` TINYINT default 0,
                    `percent_third` TINYINT default 0,
                    `background_pic` varchar(300),
                    `position_x` TINYINT default 0,
                    `position_y` TINYINT default 0,
                    `position_x_name` TINYINT default 0,
                    `position_y_name` TINYINT default 0,
                    `position_x_avatar` TINYINT default 0,
                    `position_y_avatar` TINYINT default 0,
                    `del` tinyint default 1,
                    `create_at` int,
                    `update_at` int,
                    `delete_at` int,
                    key(`uniacid`),
                    primary key(`id`)
                )engine=InnoDB AUTO_INCREMENT=1 DEFAULT charset=utf8mb4 comment="分销设置表"
            ';
            DB::statement($table);

            DB::table('setting_distribute')->insert(['uniacid'=>$this->uniacid,'create_at'=>time()]);

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = '2.0.6';
            $log->content = '升级系统分销功能';
            $log->saveLog();
            $is_update = true;
        }

        //2.0.7版本
        $q = DB::table('log')->where('cate',157)->where('remark','2.0.7')->where('uniacid',$this->uniacid)->first();
        if (!$q) {

            DB::statement('ALTER TABLE jishi_lincec_master add COLUMN base_order int default 0;');

            $table = '
                CREATE TABLE IF NOT EXISTS `jishi_lincec_master_performance`(
                    `id` int(6) unsigned not null AUTO_INCREMENT,
                    `uniacid` int,
                    `master_id` int,
                    `jiazhonglv` TINYINT default 0,
                    `complete_order` int default 0,
                    `jiazhong_order` int default 0,
                    `eva_order` int default 0,
                    `review` int default 0,
                    `collect` int default 0,
                    `score` int default 5,
                    `del` tinyint default 1,
                    `create_at` int,
                    `update_at` int,
                    `delete_at` int,
                    key(`uniacid`),
                    primary key(`id`)
                )engine=InnoDB AUTO_INCREMENT=1 DEFAULT charset=utf8mb4 comment="师傅业绩表"
            ';
            DB::statement($table);

            $list = DB::table('master')->where('del',1)->get();
            foreach ($list as $k=>$v){
                $inc = [
                    'uniacid'=>$v->uniacid,
                    'create_at'=>time(),
                    'master_id'=>$v->id,
                    'jiazhonglv'=>$v->jiazhonglv,
                    'complete_order'=>$v->complete_order,
                    'jiazhong_order'=>$v->jiazhong_order,
                    'eva_order'=>$v->eva_order,
                    'review'=>$v->review,
                    'collect'=>$v->collect,
                    'score'=>$v->score,
                ];
                DB::table('master_performance')->insert($inc);
            }

            DB::statement('ALTER TABLE jishi_lincec_master drop COLUMN complete_order;');
            DB::statement('ALTER TABLE jishi_lincec_master drop COLUMN jiazhonglv;');
            DB::statement('ALTER TABLE jishi_lincec_master drop COLUMN jiazhong_order;');
            DB::statement('ALTER TABLE jishi_lincec_master drop COLUMN eva_order;');
            DB::statement('ALTER TABLE jishi_lincec_master drop COLUMN review;');
            DB::statement('ALTER TABLE jishi_lincec_master drop COLUMN collect;');
            DB::statement('ALTER TABLE jishi_lincec_master drop COLUMN score;');

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = '2.0.7';
            $log->content = '优化升级技师相关功能，';
            $log->saveLog();
            $is_update = true;
        }

        //2.0.8版本
        $q = DB::table('log')->where('cate',157)->where('remark','2.0.8')->where('uniacid',$this->uniacid)->first();
        if (!$q) {
            $table = '
                CREATE TABLE IF NOT EXISTS `jishi_lincec_distribute_mch`(
                    `id` int(6) unsigned not null AUTO_INCREMENT,
                    `uniacid` int,
                    `user_id` int,
                    `role` varchar(32),
                    `name` varchar(32),
                    `tel` varchar(32),
                    `province` VARCHAR(30),
                    `city` VARCHAR(30),
                    `district` VARCHAR(30),
                    `apply_reason` varchar(300),
                    `check_remrk` varchar(300),
                    `check_status` tinyint default 0,
                    `status` tinyint default 0,
                    `del` tinyint default 1,
                    `create_at` int,
                    `update_at` int,
                    `delete_at` int,
                    key(`uniacid`),
                    primary key(`id`)
                )engine=InnoDB AUTO_INCREMENT=1 DEFAULT charset=utf8mb4 comment="分销商表"
            ';
            DB::statement($table);

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = '2.0.8';
            $log->content = '新增分销商功能';
            $log->saveLog();
            $is_update = true;
        }

        //2.0.9版本
        $q = DB::table('log')->where('cate',157)->where('remark','2.0.9')->where('uniacid',$this->uniacid)->first();
        if (!$q) {
            $table = '
                CREATE TABLE IF NOT EXISTS `jishi_lincec_distribute_mch_performance`(
                    `id` int(6) unsigned not null AUTO_INCREMENT,
                    `uniacid` int,
                    `user_id` int,
                    `distribute_mch_id` int,
                    `total_invite` int,
                    `total_invite_first` int,
                    `total_invite_second` int,
                    `total_invite_third` int,
                    `total_order` int,
                    `total_order_first` int,
                    `total_order_second` int,
                    `total_order_third` int,
                    `total_fee` decimal(10,2),
                    `total_fee_first` decimal(10,2),
                    `total_fee_second` decimal(10,2),
                    `total_fee_third` decimal(10,2),
                    `del` tinyint default 1,
                    `create_at` int,
                    `update_at` int,
                    `delete_at` int,
                    key(`uniacid`),
                    primary key(`id`)
                )engine=InnoDB AUTO_INCREMENT=1 DEFAULT charset=utf8mb4 comment="分销商经营表"
            ';
            DB::statement($table);

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = '2.0.9';
            $log->content = '新增分销商订单佣金功能';
            $log->saveLog();
            $is_update = true;
        }

        //2.1.1版本
        $q = DB::table('log')->where('cate',157)->where('remark','2.1.1')->where('uniacid',$this->uniacid)->first();
        if (!$q) {
            DB::statement('ALTER TABLE jishi_lincec_master_performance add COLUMN service_fee decimal(10,2) default 0;');
            DB::statement('ALTER TABLE jishi_lincec_master_performance add COLUMN jiazhong_fee decimal(10,2) default 0;');
            DB::statement('ALTER TABLE jishi_lincec_master_performance add COLUMN travel_fee decimal(10,2) default 0;');

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = '2.1.1';
            $log->content = '新增师傅经营数据';
            $log->saveLog();
            $is_update = true;
        }

        //2.1.2版本
        $q = DB::table('log')->where('cate',157)->where('remark','2.1.2')->where('uniacid',$this->uniacid)->first();
        if (!$q) {
            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = '2.1.2';
            $log->content = '升级支付组件';
            $log->saveLog();
            $is_update = true;
        }

        //2.1.3版本
        $q = DB::table('log')->where('cate',157)->where('remark','2.1.3')->where('uniacid',$this->uniacid)->first();
        if (!$q) {
            DB::statement('ALTER TABLE jishi_lincec_setting add COLUMN app_wechat_appid varchar(100);');
            DB::statement('ALTER TABLE jishi_lincec_setting add COLUMN app_wechat_appsecret varchar(100);');
            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = '2.1.3';
            $log->content = '新增APP端微信授权登录功能';
            $log->saveLog();
            $is_update = true;
        }


        $version = '2.1.4';
        $q = DB::table('log')->where('cate',157)->where('remark',$version)->where('uniacid',$this->uniacid)->first();
        if (!$q) {
            DB::statement('ALTER TABLE jishi_lincec_user add COLUMN channel tinyint(4) default 1;');

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = $version;
            $log->content = '新增用户渠道';
            $log->saveLog();
            $is_update = true;
        }

        $version = '2.1.5';
        $q = DB::table('log')->where('cate',157)->where('remark',$version)->where('uniacid',$this->uniacid)->first();
        if (!$q) {

            DB::statement('ALTER TABLE jishi_lincec_setting add COLUMN register_top_pic varchar(300);');
            DB::statement('ALTER TABLE jishi_lincec_setting add COLUMN master_rest_text varchar(10);');
            DB::statement('ALTER TABLE jishi_lincec_setting add COLUMN travel_fee_text varchar(10);');

            $table = '
                CREATE TABLE IF NOT EXISTS `jishi_lincec_master_service`(
                    `id` int(6) unsigned not null AUTO_INCREMENT,
                    `uniacid` int,
                    `master_id` int,
                    `item_id` int,
                    `status` tinyint default 0,
                    `del` tinyint default 1,
                    `create_at` int,
                    `update_at` int,
                    `delete_at` int,
                    key(`uniacid`),
                    primary key(`id`)
                )engine=InnoDB AUTO_INCREMENT=1 DEFAULT charset=utf8mb4 comment="师傅服务项目表"
            ';
            DB::statement($table);

            $m = DB::table('master')->where('del',1)->where('status',1)->get()->toArray();
            $i = DB::table('item')->where('del',1)->where('status',1)->get()->toArray();
            foreach ($m as $k=>$v){
                foreach ($i as $k1=>$v1){
                    $inc = ['uniacid'=>$this->uniacid,'master_id'=>$v->id,'item_id'=>$v1->id,'create_at'=>time()];
                    DB::table('master_service')->insert($inc);
                }
            }

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = $version;
            $log->content = '支持师傅自选服务项目、后台自定义设置技师入驻顶图';
            $log->saveLog();
            $is_update = true;
        }

        $version = '2.1.6';
        $q = DB::table('log')->where('cate',157)->where('remark',$version)->where('uniacid',$this->uniacid)->first();
        if (!$q) {
            DB::statement('ALTER TABLE jishi_lincec_admin add COLUMN privilege_cate tinyint(4) default 2 comment "1超级权限2自定义权限";');

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = $version;
            $log->content = '新增一键超级权限、新增评价管理模块';
            $log->saveLog();
            $is_update = true;
        }

        $version = '2.1.7';
        $q = DB::table('log')->where('cate',157)->where('remark',$version)->where('uniacid',$this->uniacid)->first();
        if (!$q) {
            DB::statement('ALTER TABLE jishi_lincec_eva_record add COLUMN master_id int;');
            DB::statement('ALTER TABLE jishi_lincec_eva_record add COLUMN item_id int;');
            DB::statement('ALTER TABLE jishi_lincec_eva_record add COLUMN order_id int;');

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = $version;
            $log->content = '优化评价流程';
            $log->saveLog();
            $is_update = true;
        }

        $version = '2.1.8';
        $q = DB::table('log')->where('cate',157)->where('remark',$version)->where('uniacid',$this->uniacid)->first();
        if (!$q) {
            DB::statement('ALTER TABLE jishi_lincec_setting add COLUMN privacy_tel_status tinyint default 0;');
            DB::statement('ALTER TABLE jishi_lincec_setting add COLUMN privacy_tel_notify_status tinyint default 0;');
            DB::statement('ALTER TABLE jishi_lincec_setting add COLUMN privacy_tel_appid varchar(100);');
            DB::statement('ALTER TABLE jishi_lincec_setting add COLUMN privacy_tel_token varchar(100);');
            DB::statement('ALTER TABLE jishi_lincec_setting add COLUMN privacy_tel_number varchar(100);');
            DB::statement('ALTER TABLE jishi_lincec_setting add COLUMN privacy_tel_notify_number varchar(100);');
            DB::statement('ALTER TABLE jishi_lincec_setting add COLUMN privacy_tel_notify_template_id varchar(100);');
            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = $version;
            $log->content = '新增隐私电话、新订单电话语音通知、日志联动查询等功能';
            $log->saveLog();
            $is_update = true;
        }

        $version = '2.1.9';
        $q = DB::table('log')->where('cate',157)->where('remark',$version)->where('uniacid',$this->uniacid)->first();
        if (!$q) {
            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = $version;
            $log->content = '支持腾讯地图、高德地图、百度地图等地图自由选择打开导航功能';
            $log->saveLog();
            $is_update = true;
        }



        $version = '2.2.1';
        $q = DB::table('log')->where('cate',157)->where('remark',$version)->where('uniacid',$this->uniacid)->first();
        if (!$q) {
            DB::statement('ALTER TABLE jishi_lincec_setting add COLUMN open_auth_phone tinyint default 0;');

            $table = '
                CREATE TABLE IF NOT EXISTS `jishi_lincec_sms_setting`(
                    `id` int(6) unsigned not null AUTO_INCREMENT,
                    `uniacid` int,
                    `appcode` varchar(32),
                    `smsSignId` varchar(32),
                    `templateId` varchar(32),
                    `del` tinyint default 1,
                    `create_at` int,
                    `update_at` int,
                    `delete_at` int,
                    key(`uniacid`),
                    primary key(`id`)
                )engine=InnoDB AUTO_INCREMENT=1 DEFAULT charset=utf8mb4 comment="短信配置表"
            ';
            DB::statement($table);


            $table = '
                CREATE TABLE IF NOT EXISTS `jishi_lincec_sms_verification`(
                    `id` int(6) unsigned not null AUTO_INCREMENT,
                    `uniacid` int,
                    `phone_number` varchar(32),
                    `code` varchar(32),
                    `status` tinyint default 0 comment "0待验证1已验证2已过期",
                    `del` tinyint default 1,
                    `create_at` int,
                    `update_at` int,
                    `delete_at` int,
                    key(`uniacid`),
                    primary key(`id`)
                )engine=InnoDB AUTO_INCREMENT=1 DEFAULT charset=utf8mb4 comment="短信验证表"
            ';
            DB::statement($table);

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = $version;
            $log->content = '新增短信验证码功能';
            $log->saveLog();
            $is_update = true;
        }


        $version = '2.2.2';
        $q = DB::table('log')->where('cate',157)->where('remark',$version)->where('uniacid',$this->uniacid)->first();
        if (!$q) {

            DB::statement('ALTER TABLE jishi_lincec_distribute_mch add COLUMN open_percent tinyint default 0;');
            DB::statement('ALTER TABLE jishi_lincec_distribute_mch add COLUMN percent_first tinyint default 0;');
            DB::statement('ALTER TABLE jishi_lincec_distribute_mch add COLUMN percent_second tinyint default 0;');
            DB::statement('ALTER TABLE jishi_lincec_distribute_mch add COLUMN percent_third tinyint default 0;');

            $table = '
                CREATE TABLE IF NOT EXISTS `jishi_lincec_distribute_qrcode`(
                    `id` int(6) unsigned not null AUTO_INCREMENT,
                    `uniacid` int,
                    `distribute_mch_id` int,
                    `serial_number` varchar(32),
                    `pic` varchar(300),
                    `token` varchar(300),
                    `status` tinyint default 0,
                    `del` tinyint default 1,
                    `bind_at` int,
                    `create_at` int,
                    `update_at` int,
                    `delete_at` int,
                    key(`uniacid`),
                    primary key(`id`)
                )engine=InnoDB AUTO_INCREMENT=1 DEFAULT charset=utf8mb4 comment="分销二维码表"
            ';
            DB::statement($table);

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = $version;
            $log->content = '新增酒店分销/批量生成分销码等功能';
            $log->saveLog();
            $is_update = true;
        }

        $version = '2.2.3';
        $q = DB::table('log')->where('cate',157)->where('remark',$version)->where('uniacid',$this->uniacid)->first();
        if (!$q) {

            DB::statement('ALTER TABLE jishi_lincec_log add COLUMN detail text;');

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = $version;
            $log->content = '优化日志功能';
            $log->saveLog();
            $is_update = true;
        }

        $version = '2.2.4';
        $q = DB::table('log')->where('cate',157)->where('remark',$version)->where('uniacid',$this->uniacid)->first();
        if (!$q) {

            $table = '
                CREATE TABLE IF NOT EXISTS `jishi_lincec_city`(
                    `id` int(6) unsigned not null AUTO_INCREMENT,
                    `uniacid` int,
                    `type` int,
                    `name` varchar(32),
                    `_sort` int default 0,
                    `status` tinyint default 1,
                    `del` tinyint default 1,
                    `bind_at` int,
                    `create_at` int,
                    `update_at` int,
                    `delete_at` int,
                    key(`uniacid`),
                    primary key(`id`)
                )engine=InnoDB AUTO_INCREMENT=1 DEFAULT charset=utf8mb4 comment="城市表"
            ';
            DB::statement($table);

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = $version;
            $log->content = '新增城市自定义功能';
            $log->saveLog();
            $is_update = true;
        }


        $version = '2.2.5';
        $q = DB::table('log')->where('cate',157)->where('remark',$version)->where('uniacid',$this->uniacid)->first();
        if (!$q) {

            DB::statement('ALTER TABLE jishi_lincec_distribute_qrcode add COLUMN background_pic varchar(300);');
            DB::statement('ALTER TABLE jishi_lincec_distribute_qrcode add COLUMN remark varchar(300);');
            DB::statement('ALTER TABLE jishi_lincec_distribute_qrcode add COLUMN percent_first TINYINT default 0;');
            DB::statement('ALTER TABLE jishi_lincec_distribute_qrcode add COLUMN percent_second TINYINT default 0;');
            DB::statement('ALTER TABLE jishi_lincec_distribute_qrcode add COLUMN percent_third TINYINT default 0;');
            DB::statement('ALTER TABLE jishi_lincec_distribute_qrcode add COLUMN position_x TINYINT default 0;');
            DB::statement('ALTER TABLE jishi_lincec_distribute_qrcode add COLUMN position_y TINYINT default 0;');
            DB::statement('ALTER TABLE jishi_lincec_distribute_qrcode add COLUMN position_x_remark TINYINT default 0;');
            DB::statement('ALTER TABLE jishi_lincec_distribute_qrcode add COLUMN position_y_remark TINYINT default 0;');

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = $version;
            $log->content = '升级酒店分销码、高德地图等功能';
            $log->saveLog();
            $is_update = true;
        }

        $version = '2.2.6';
        $q = DB::table('log')->where('cate',157)->where('remark',$version)->where('uniacid',$this->uniacid)->first();
        if (!$q) {

            DB::statement('ALTER TABLE jishi_lincec_user add COLUMN get_tel_time int;');

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = $version;
            $log->content = '新增号码管理菜单功能';
            $log->saveLog();
            $is_update = true;
        }


        $version = '2.2.7';
        $q = DB::table('log')->where('cate',157)->where('remark',$version)->where('uniacid',$this->uniacid)->first();
        if (!$q) {

            DB::statement('ALTER TABLE jishi_lincec_distribute_qrcode add COLUMN create_record_id INT;');

            $table = '
                CREATE TABLE IF NOT EXISTS `jishi_lincec_qrcode_create_record`(
                    `id` int(6) unsigned not null AUTO_INCREMENT,
                    `uniacid` int,
                    `admin_id` int,
                    `num` int,
                    `remark` varchar(300),
                    `percent_first` TINYINT default 0,
                    `percent_second` TINYINT default 0,
                    `percent_third` TINYINT default 0,
                    `background_pic` varchar(300),
                    `position_x` TINYINT default 0,
                    `position_y` TINYINT default 0,
                    `position_x_remark` TINYINT default 0,
                    `position_y_remark` TINYINT default 0,
                    `del` tinyint default 1,
                    `create_at` int,
                    `update_at` int,
                    `delete_at` int,
                    key(`uniacid`),
                    primary key(`id`)
                )engine=InnoDB AUTO_INCREMENT=1 DEFAULT charset=utf8mb4 comment="分销码生成记录表"
            ';
            DB::statement($table);

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = $version;
            $log->content = '新增分销码生成记录功能';
            $log->saveLog();
            $is_update = true;
        }

        $version = '2.2.8';
        $q = DB::table('log')->where('cate',157)->where('remark',$version)->where('uniacid',$this->uniacid)->first();
        if (!$q) {

            DB::statement('ALTER TABLE jishi_lincec_sms_setting add COLUMN template_notice_id varchar(100);');
            DB::statement('ALTER TABLE jishi_lincec_sms_setting add COLUMN order_notice tinyint default 0;');

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = $version;
            $log->content = '新增短信通知功能';
            $log->saveLog();
            $is_update = true;
        }

        $version = '2.2.9';
        $q = DB::table('log')->where('cate',157)->where('remark',$version)->where('uniacid',$this->uniacid)->first();
        if (!$q) {

            DB::statement('ALTER TABLE jishi_lincec_master add COLUMN auto_taking tinyint default 0;');

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = $version;
            $log->content = '丰富订单流程，新增自动接单控制功能';
            $log->saveLog();
            $is_update = true;
        }

        $version = '2.3.1';
        $q = DB::table('log')->where('cate',157)->where('remark',$version)->where('uniacid',$this->uniacid)->first();
        if (!$q) {
            DB::statement('ALTER TABLE jishi_lincec_jiazhong_record add COLUMN item_id int default 0;');
            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = $version;
            $log->content = '自由选择加钟项目功能';
            $log->saveLog();
            $is_update = true;
        }


//        $version = '2.3.2';
//        $q = DB::table('log')->where('cate',157)->where('remark',$version)->where('uniacid',$this->uniacid)->first();
//        if (!$q) {
//
//            $table = '
//                CREATE TABLE IF NOT EXISTS `jishi_lincec_order_upgrade`(
//                    `id` int unsigned not null AUTO_INCREMENT,
//                    `uniacid` int,
//                    `order_id` int,
//                    `item_id` int,
//                    `price` decimal(10,2),
//                    `status` tinyint default 1,
//                    `del` tinyint default 1,
//                    `create_at` int,
//                    `update_at` int,
//                    `delete_at` int,
//                    key(`uniacid`),
//                    primary key(`id`)
//                )engine=InnoDB AUTO_INCREMENT=1 DEFAULT charset=utf8mb4 comment="订单升级表"
//            ';
//            DB::statement($table);
//
//            $log->uniacid = $this->uniacid;
//            $log->user_id = $this->admin_id;
//            $log->event = 'updateDBVersion';
//            $log->remark = $version;
//            $log->content = '新增订单升级功能';
//            $log->saveLog();
//            $is_update = true;
//        }

        $version = '2.3.3';
        $q = DB::table('log')->where('cate',157)->where('remark',$version)->where('uniacid',$this->uniacid)->first();
        if (!$q) {

            $table = '
                CREATE TABLE IF NOT EXISTS `jishi_lincec_user_relation`(
                    `id` int unsigned not null AUTO_INCREMENT,
                    `uniacid` int,
                    `user_id` int,
                    `belong_id` int,
                    `type` tinyint,
                    `del` tinyint default 1,
                    `create_at` int,
                    `update_at` int,
                    `delete_at` int,
                    key(`uniacid`),
                    primary key(`id`)
                )engine=InnoDB AUTO_INCREMENT=1 DEFAULT charset=utf8mb4 comment="用户分销关系表"
            ';
            DB::statement($table);

            //关系写入
            $list = DB::table('user')->where('uniacid',$this->uniacid)->where('del',1)->get();
            foreach ($list as $k=>$v){
                if(!$v->pre_id)continue ;
                $inc = [
                    'uniacid'=>$this->uniacid,
                    'user_id'=>$v->id,
                    'belong_id'=>$v->pre_id,
                    'type'=>1,
                    'create_at'=>time()
                ];
                $q = DB::table('user_relation')->where('user_id',$inc['user_id'])->where('belong_id',$inc['belong_id'])->where('del',1)->first();
                if(!$q){ 
                    DB::table('user_relation')->insert($inc);
                }

                $u = DB::table('user')->where('id',$v->pre_id)->where('uniacid',$this->uniacid)->where('del',1)->first();
                if($u->pre_id){

                }


            }

            $log->uniacid = $this->uniacid;
            $log->user_id = $this->admin_id;
            $log->event = 'updateDBVersion';
            $log->remark = $version;
            $log->content = '新增用户关系表';
            $log->saveLog();
            $is_update = true;
        }


        if(!$is_update)return $this->apiSuccess('已是最新版本');

        return $this->apiSuccess('已更新至最新版本');

    }


}
