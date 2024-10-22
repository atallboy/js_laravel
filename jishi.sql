
drop table if exists `jishi_lincec_access_token`;
CREATE TABLE `jishi_lincec_access_token` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `uniacid` int,
      `app_id` varchar(32),
      `access_token` varchar(300),
      `invalid_time` int,
      `status` tinyint DEFAULT 1,
      `del` tinyint DEFAULT 1,
      `create_at` int,
      `delete_at` int,
      `update_at` int,
      PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 comment='表';

drop table if exists `jishi_lincec_site`;
CREATE TABLE `jishi_lincec_site` (
         `id` int(11) NOT NULL AUTO_INCREMENT,
         `name` varchar(32),
         `desc` varchar(100),
         `status` tinyint DEFAULT 1,
         `del` tinyint DEFAULT 1,
         `create_at` int,
         `delete_at` int,
         `update_at` int,
         PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 comment='站点表';

drop table if exists `jishi_lincec_setting`;
CREATE TABLE `jishi_lincec_setting` (
             `id` int(11) NOT NULL AUTO_INCREMENT,
             `uniacid` int,
             `name` varchar(30),
             `logo` varchar(300),
             `tel` varchar(300),
             `desc` text,
             `app_id` varchar(32),
             `app_secret` varchar(32),
             `gzh_appid` varchar(32),
             `gzh_appsecret` varchar(32),
             `mch_id` varchar(32),
             `mch_secret` varchar(32),
             `mch_cert_pem` text,
             `mch_key_pem` text,
             `master_percent` tinyint,
             `agent_percent` tinyint,
             `distribute_coupon` int,
             `eva_tag` varchar(1000),
             `open_service_link` tinyint DEFAULT 0,
             `service_link` varchar(300),
             `city_arr` varchar(3000),
             `access_time` int,
             `access_token` varchar(300),
             `status` tinyint DEFAULT 1,
             `del` tinyint DEFAULT 1,
             `create_at` int,
             `delete_at` int,
             `update_at` int,
             PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 comment='系统设置表';

drop table if exists `jishi_lincec_admin`;
CREATE TABLE IF NOT EXISTS `jishi_lincec_admin`(
    `id` int(6) unsigned not null AUTO_INCREMENT,
    `uniacid` int,
    `role_id` int,
    `token` varchar(32),
    `name` varchar(32),
    `account` varchar(32),
    `password` varchar(32),
    `status` tinyint default 0,
    `del` tinyint default 1,
    `create_at` int,
    `update_at` int,
    `delete_at` int,
    key(`uniacid`),
    primary key(`id`)
    )engine=InnoDB AUTO_INCREMENT=1 DEFAULT charset=utf8mb4 comment='管理员表';
INSERT INTO jishi_lincec_admin (id,uniacid,role_id,token,name,account,password) VALUES (1,1,1,'a38c4a5d7c576da8a63db757570555f7','admin','admin','e6a9ee93bcadd388194017b4b89d5fe6');

drop table if exists `jishi_lincec_user`;
CREATE TABLE `jishi_lincec_user`(
         `id` int NOT NULL AUTO_INCREMENT,
         `uniacid` int,
         `pre_id` int,
         `token` VARCHAR(32),
         `openid` varchar(50) NOT NULL,
         `session_key` varchar(50) NOT NULL,
         `unionid` varchar(50) NOT NULL,
         `nickName` varchar(128) DEFAULT NULL,
         `avatarUrl` varchar(300) DEFAULT NULL,
         `total_balance` decimal(10,2) default 0,
         `balance` decimal(10,2) default 0,
         `withdrawal` decimal(10,2) default 0,
         `integral` int default 0,
         `tel` VARCHAR(30),
         `country` varchar(50) DEFAULT NULL,
         `province` varchar(50) DEFAULT NULL,
         `city` varchar(50) DEFAULT NULL,
         `gender` tinyint,
         `qrcode` varchar(100),
         `qrcode_gzh` varchar(300),
         `status` TINYINT DEFAULT 1,
         `del` TINYINT DEFAULT 1,
         `delete_at` int DEFAULT NULL,
         `create_at` int DEFAULT NULL,
         `update_at` int DEFAULT NULL,
         `recent_at` int DEFAULT NULL,
         PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='用户信息表';

drop table if exists `jishi_lincec_user_gzh`;
CREATE TABLE `jishi_lincec_user_gzh`(
         `id` int NOT NULL AUTO_INCREMENT,
         `uniacid` int,
         `user_id` int,
         `openid` varchar(50) DEFAULT NULL,
         `unionid` varchar(50) DEFAULT NULL,
         `subscribe_scene` varchar(50) DEFAULT NULL,
         `subscribe` tinyint,
         `subscribe_time` int,
         `sex` tinyint,
         `del` TINYINT DEFAULT 1,
         `create_at` int DEFAULT NULL,
         PRIMARY KEY (`id`),
         KEY (`openid`),
         KEY (`unionid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='公众号用户表';


drop table if exists `jishi_lincec_item`;
CREATE TABLE `jishi_lincec_item`(
        `id` int NOT NULL AUTO_INCREMENT,
        `uniacid` int,
        `cate_id` int default 0,
        `name` VARCHAR(32),
        `price` decimal(10,2),
        `old_price` decimal(10,2),
        `pic` VARCHAR(300),
        `long_time` VARCHAR(32),
        `desc` VARCHAR(20),
        `tag` VARCHAR(100),
        `range_people` VARCHAR(32),
        `gender` TINYINT DEFAULT 1,
        `detail` text,
        `base_sale` int DEFAULT 0,
        `real_sale` int DEFAULT 0,
        `base_collect` int DEFAULT 0,
        `real_collect` int DEFAULT 0,
        `_sort` int DEFAULT NULL,
        `status` TINYINT DEFAULT 1,
        `del` TINYINT DEFAULT 1,
        `delete_at` int DEFAULT NULL,
        `create_at` int DEFAULT NULL,
        `update_at` int DEFAULT NULL,
        PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='服务项目表';

drop table if exists `jishi_lincec_master`;
CREATE TABLE `jishi_lincec_master`(
        `id` int NOT NULL AUTO_INCREMENT,
        `uniacid` int,
        `cate` TINYINT DEFAULT 1,
        `user_id` int,
        `name` VARCHAR(32),
        `pic` VARCHAR(300),
        `tel` VARCHAR(20),
        `desc` VARCHAR(1000),
        `province` varchar(30),
        `city` varchar(30),
        `district` varchar(30),
        `address` varchar(200),
        `age` TINYINT DEFAULT 1,
        `gender` TINYINT DEFAULT 1,
        `latitude` varchar(30),
        `longitude` varchar(30),
        `store_name` VARCHAR(32),
        `certificate` varchar(1000),
        `qrcode` varchar(100),
        `qrcode_gzh` varchar(300),
        `order` int DEFAULT 0,
        `base_review` int DEFAULT 0,
        `review` int DEFAULT 0,
        `base_collect` int DEFAULT 0,
        `collect` int DEFAULT 0,
        `_sort` int DEFAULT NULL,
        `start_time` int,
        `end_time` int,
        `travel_expense` tinyint default 0,
        `taxi_fee` decimal(10,2) default 0,
        `bus_fee` decimal(10,2) default 0,
        `jiazhonglv` TINYINT default 0,
        `complete_order` int default 0,
        `jiazhong_order` int default 0,
        `eva_order` int default 0,
        `half_year_complete_order` int default 0,
        `score` decimal(3,1) default 5.0,
        `status` TINYINT DEFAULT 1,
        `is_recommend` TINYINT DEFAULT 1,
        `is_excellent` TINYINT DEFAULT 0,
        `is_hot` TINYINT DEFAULT 0,
        `is_fast` TINYINT DEFAULT 0,
        `open_status` TINYINT DEFAULT 0,
        `del` TINYINT DEFAULT 1,
        `delete_at` int DEFAULT NULL,
        `create_at` int DEFAULT NULL,
        `update_at` int DEFAULT NULL,
        PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='技师表';

drop table if exists `jishi_lincec_master_collect`;
CREATE TABLE `jishi_lincec_master_collect`(
      `id` int NOT NULL AUTO_INCREMENT,
      `uniacid` int,
      `user_id` int,
      `master_id` int,
      `del` TINYINT DEFAULT 1,
      `delete_at` int DEFAULT NULL,
      `create_at` int DEFAULT NULL,
      `update_at` int DEFAULT NULL,
      PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='技师收藏表';

drop table if exists `jishi_lincec_address`;
create table `jishi_lincec_address`(
         `id` int unsigned not null AUTO_INCREMENT,
         `uniacid` int,
         `user_id` int,
         `name` varchar(30),
         `tel` varchar(30),
         `province` varchar(30),
         `city` varchar(30),
         `district` varchar(30),
         `address` varchar(200),
         `remark` varchar(100),
         `latitude` varchar(30),
         `longitude` varchar(30),
         `is_default` tinyint default 0,
         `status` tinyint default 1,
         `del` tinyint default 1,
         `create_at` int,
         `update_at` int,
         `delete_at` int,
         primary key(`id`)
)engine=InnoDB AUTO_INCREMENT=1 DEFAULT charset=utf8mb4 comment='地址表';

drop table if exists `jishi_lincec_paylog`;
CREATE TABLE `jishi_lincec_paylog`(
       `id` int NOT NULL AUTO_INCREMENT,
       `uniacid` int,
       `user_id` int,
       `cate` tinyint DEFAULT 0 comment '1服务订单2充值订单3加钟订单',
       `fee` decimal(10,2) default 0,
       `pay_fee` decimal(10,2) default 0,
       `status` tinyint DEFAULT 0,
       `order_no` VARCHAR(32),
       `out_trade_no` VARCHAR(32),
       `remark` VARCHAR(100),
       `notify_result` text,
       `del` tinyint DEFAULT 1,
       `delete_at` int DEFAULT NULL,
       `create_at` int DEFAULT NULL,
       `update_at` int DEFAULT NULL,
       PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='支付表';

drop table if exists `jishi_lincec_order`;
CREATE TABLE `jishi_lincec_order`(
          `id` int NOT NULL AUTO_INCREMENT,
          `uniacid` int,
          `user_id` int,
          `address_id` int,
          `master_id` int,
          `is_jiazhong` TINYINT DEFAULT 0,
          `pay_type` TINYINT DEFAULT 0,
          `total_fee` decimal(10,2) default 0,
          `pay_fee` decimal(10,2) default 0,
          `product_fee` decimal(10,2) default 0,
          `coupon_id` int,
          `coupon_fee` decimal(10,2) default 0,
          `travel_type` TINYINT DEFAULT 0 comment '1出租2公交',
          `travel_fee` decimal(10,2) default 0,
          `remark` varchar(100),
          `service_time` varchar(50),
          `snap_name` varchar(30),
          `snap_pic` varchar(300),
          `status` TINYINT DEFAULT 0,
          `del` TINYINT DEFAULT 1,
          `delete_at` int DEFAULT NULL,
          `create_at` int DEFAULT NULL,
          `update_at` int DEFAULT NULL,
          `complete_at` int DEFAULT NULL,
          PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='订单表';

drop table if exists `jishi_lincec_order_detail`;
CREATE TABLE `jishi_lincec_order_detail`(
         `id` int NOT NULL AUTO_INCREMENT,
         `uniacid` int,
         `order_id` int,
         `name` varchar(30),
         `tel` varchar(30),
         `province` varchar(30),
         `city` varchar(30),
         `district` varchar(30),
         `address` varchar(200),
         `remark` varchar(100),
         `latitude` varchar(30),
         `longitude` varchar(30),
         `del` TINYINT DEFAULT 1,
         `delete_at` int DEFAULT NULL,
         `create_at` int DEFAULT NULL,
         `update_at` int DEFAULT NULL,
         PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='订单详情表';

drop table if exists `jishi_lincec_order_product`;
CREATE TABLE `jishi_lincec_order_product`(
             `id` int NOT NULL AUTO_INCREMENT,
             `uniacid` int,
             `order_id` int,
             `item_id` int,
             `num` int,
             `total_price` decimal(10,2) default 0,
             `status` TINYINT DEFAULT 0,
             `del` TINYINT DEFAULT 1,
             `delete_at` int DEFAULT NULL,
             `create_at` int DEFAULT NULL,
             `update_at` int DEFAULT NULL,
             PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='订单产品表';

drop table if exists `jishi_lincec_order_settle`;
CREATE TABLE `jishi_lincec_order_settle`(
         `id` int NOT NULL AUTO_INCREMENT,
         `uniacid` int,
         `order_id` int,
         `platform_fee` decimal(10,2) default 0,
         `master_fee` decimal(10,2) default 0,
         `agent_fee` decimal(10,2) default 0,
         `agent_avg_fee` decimal(10,2) default 0,

         `del` TINYINT DEFAULT 1,
         `delete_at` int DEFAULT NULL,
         `create_at` int DEFAULT NULL,
         `update_at` int DEFAULT NULL,
         PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='订单结算表';

drop table if exists `jishi_lincec_jiazhong_record`;
CREATE TABLE `jishi_lincec_jiazhong_record`(
       `id` int NOT NULL AUTO_INCREMENT,
       `uniacid` int,
       `user_id` int,
       `order_id` int,

       `product_money` decimal(10,2) default 0,
       `status` TINYINT DEFAULT 0,
       `del` TINYINT DEFAULT 1,
       `delete_at` int DEFAULT NULL,
       `create_at` int DEFAULT NULL,
       `update_at` int DEFAULT NULL,
       PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='加钟记录表';

drop table if exists `jishi_lincec_recharge_record`;
CREATE TABLE `jishi_lincec_recharge_record`(
             `id` int NOT NULL AUTO_INCREMENT,
             `uniacid` int,
             `user_id` int,
             `recharge_id` int,
             `money` decimal(10,2) default 0,
             `status` TINYINT DEFAULT 0,
             `del` TINYINT DEFAULT 1,
             `delete_at` int DEFAULT NULL,
             `create_at` int DEFAULT NULL,
             `update_at` int DEFAULT NULL,
             PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='充值记录表';

drop table if exists `jishi_lincec_suggest`;
CREATE TABLE `jishi_lincec_suggest`(
           `id` int NOT NULL AUTO_INCREMENT,
           `uniacid` int,
           `user_id` int,
           `content` varchar(100),
           `pic` varchar(3000),
           `remark` varchar(300),
           `status` TINYINT DEFAULT 0,
           `del` TINYINT DEFAULT 1,
           `delete_at` int DEFAULT NULL,
           `create_at` int DEFAULT NULL,
           `update_at` int DEFAULT NULL,
           PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='投诉建议表';

drop table if exists `jishi_lincec_banner`;
CREATE TABLE `jishi_lincec_banner` (
         `id` int NOT NULL AUTO_INCREMENT,
         `uniacid` int,
         `cate` tinyint DEFAULT 1 comment '1banner,2toast',
         `name` varchar(100),
         `pic` varchar(300),
         `type` tinyint default 1 comment '0无跳转1跳转内部2跳转外部网页',
         `url` varchar(30),
         `param` varchar(30),
         `_sort` int,
         `status` tinyint DEFAULT 1,
         `del` tinyint DEFAULT 1,
         `create_at` int,
         `delete_at` int,
         `update_at` int,
         PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 comment='轮播图表';

drop table if exists `jishi_lincec_coupon`;
CREATE TABLE `jishi_lincec_coupon` (
       `id` int NOT NULL AUTO_INCREMENT,
       `uniacid` int,
       `name` varchar(30),
       `get_type` tinyint default 1 comment '1直接领取2兑换码领取3分享领取',
       `use_range` tinyint default 1 comment '使用类目：1全场2指定分类3指定产品',
       `type` tinyint default 1 comment '1直减2满减',
       `amount` decimal(10,2) comment '优惠券面值',
       `minimum` decimal(10,2) comment '满减门槛',
       `valid_time_type` tinyint default 1 comment '有效时间方式：1固定期限2领取后计算',
       `valid_day` int comment '有效天数',
       `valid_start_time` int comment '有效开始时间',
       `valid_end_time` int comment '有效结束时间',
       `num` int comment '数量',
       `get_limit` int default 1 comment '每人可领取数',
       `redeem_code` varchar(20) comment '兑换码',
       `_sort` int DEFAULT null,
       `status` tinyint DEFAULT 1,
       `del` tinyint DEFAULT 1,
       `create_at` int,
       `delete_at` int,
       `update_at` int,
       PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 comment='优惠券表';

drop table if exists `jishi_lincec_coupon_exchange_record`;
CREATE TABLE `jishi_lincec_coupon_exchange_record` (
       `id` int NOT NULL AUTO_INCREMENT,
       `uniacid` int,
       `user_id` int,
       `coupon_id` int,
       `redeem_code` varchar(20),
       `status` tinyint DEFAULT 1,
       `del` tinyint DEFAULT 1,
       `create_at` int,
       `delete_at` int,
       `update_at` int,
       PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 comment='优惠券兑换表';

drop table if exists `jishi_lincec_coupon_record`;
CREATE TABLE `jishi_lincec_coupon_record` (
       `id` int NOT NULL AUTO_INCREMENT,
       `uniacid` int,
       `cate` tinyint default 1 comment '1兑换码领取2邀请奖励',
       `user_id` int,
       `coupon_id` int,
       `valid_start_time` int,
       `valid_end_time` int,
       `remark` varchar(100),
       `status` tinyint DEFAULT 1,
       `del` tinyint DEFAULT 1,
       `create_at` int,
       `delete_at` int,
       `update_at` int,
       PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 comment='优惠券记录表';

drop table if exists `jishi_lincec_agent`;
CREATE TABLE `jishi_lincec_agent`(
          `id` int NOT NULL AUTO_INCREMENT,
          `uniacid` int,
          `user_id` int,
          `name` varchar(30),
          `tel` VARCHAR(30),
          `province` VARCHAR(30),
          `city` VARCHAR(30),
          `district` VARCHAR(30),
          `remark` VARCHAR(100),
          `status` TINYINT DEFAULT 0,
          `order_num` int DEFAULT 0,
          `order_money` decimal(10,2) DEFAULT 0,
          `del` TINYINT DEFAULT 1,
          `delete_at` int DEFAULT NULL,
          `create_at` int DEFAULT NULL,
          `update_at` int DEFAULT NULL,
          PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='代理表';

drop table if exists `jishi_lincec_balance_record`;
CREATE TABLE IF NOT EXISTS `jishi_lincec_balance_record` (
    `id` int(6) unsigned not null AUTO_INCREMENT,
    `uniacid` int,
    `user_id` int,
    `type` tinyint default 1 COMMENT '1加2减',
    `cate` tinyint,
    `_id` int DEFAULT '0',
    `money` decimal(10,2),
    `before_balance` decimal(10,2),
    `after_balance` decimal(10,2),
    `remark` varchar(100),
    `del` tinyint DEFAULT 1,
    `create_at` int,
    `update_at` int,
    `delete_at` int,
    primary key(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='余额表';

drop table if exists `jishi_lincec_withdrawal_record`;
CREATE TABLE IF NOT EXISTS `jishi_lincec_withdrawal_record` (
    `id` int(6) unsigned not null AUTO_INCREMENT,
    `uniacid` int,
    `user_id` int,
    `money` decimal(10,2),
    `type` tinyint default 1 COMMENT '1支付宝',
    `name` varchar(100),
    `tel` varchar(100),
    `zfb_account` varchar(100),
    `remark` varchar(100),
    `status` tinyint DEFAULT 0,
    `del` tinyint DEFAULT 1,
    `create_at` int,
    `update_at` int,
    `delete_at` int,
    primary key(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='提现表';


drop table if exists `jishi_lincec_eva_record`;
CREATE TABLE IF NOT EXISTS `jishi_lincec_eva_record` (
    `id` int(6) unsigned not null AUTO_INCREMENT,
    `uniacid` int,
    `cate` tinyint default 1 comment '1技师评价2服务项目评价',
    `_id` int,
    `user_id` int,
    `score` decimal(2,1),
    `tag` varchar(1000),
    `content` varchar(1000),
    `status` tinyint DEFAULT 0,
    `del` tinyint DEFAULT 1,
    `create_at` int,
    `update_at` int,
    `delete_at` int,
    primary key(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='评价表';


drop table if exists `jishi_lincec_log`;
CREATE TABLE IF NOT EXISTS `jishi_lincec_log` (
    `id` int(6) unsigned not null AUTO_INCREMENT,
    `uniacid` int,
    `user_id` int,
    `cate` tinyint default 1 comment '',
    `_id` int,
    `remark` varchar(300),
    `content` varchar(3000),
    `del` tinyint DEFAULT 1,
    `create_at` int,
    `update_at` int,
    `delete_at` int,
    primary key(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='日志表';

drop table if exists `jishi_lincec_settle_solution`;
CREATE TABLE IF NOT EXISTS `jishi_lincec_settle_solution` (
    `id` int(6) unsigned not null AUTO_INCREMENT,
    `uniacid` int,
    `name` varchar(100),
    `settle_object` tinyint default 1 comment '结算对象：1技师结算2代理结算',
    `settle_time` tinyint default 1 comment '1每日2每周3每月4每季度5每年',
    `settle_type` tinyint default 1 comment '1手动结算2自动结算',
    `master_id` int,
    `agent_id` int,
    `status` tinyint DEFAULT 0,
    `del` tinyint DEFAULT 1,
    `create_at` int,
    `update_at` int,
    `delete_at` int,
    primary key(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='经营结算方案表';

drop table if exists `jishi_lincec_settle_solution_ladder`;
CREATE TABLE IF NOT EXISTS `jishi_lincec_settle_solution_ladder` (
    `id` int(6) unsigned not null AUTO_INCREMENT,
    `uniacid` int,
    `settle_solution_id` int,
    `min` int,
    `max` int,
    `fee_type` tinyint default 1 comment '计费项：1常规费用2加钟费用',
    `calc_type` tinyint default 1 comment '1百分比2固定金额',
    `percent` tinyint DEFAULT 0,
    `status` tinyint DEFAULT 0,
    `del` tinyint DEFAULT 1,
    `create_at` int,
    `update_at` int,
    `delete_at` int,
    primary key(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='经营结算方案阶级表';

drop table if exists `jishi_lincec_settle_time`;
CREATE TABLE IF NOT EXISTS `jishi_lincec_settle_time` (
    `id` int(6) unsigned not null AUTO_INCREMENT,
    `uniacid` int,
    `settle_solution_id` int,
    `_time` varchar(20),
    `start_time` varchar(10),
    `end_time` varchar(10),
    `del` tinyint DEFAULT 1,
    `create_at` int,
    `update_at` int,
    `delete_at` int,
    primary key(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='结算时间记录表';

drop table if exists `jishi_lincec_settle_record`;
CREATE TABLE IF NOT EXISTS `jishi_lincec_settle_record` (
    `id` int(6) unsigned not null AUTO_INCREMENT,
    `uniacid` int,
    `user_id` int,
    `settle_time_id` int,
    `order_num` int default 0,
    `achieve_money` decimal(10,2) default 0,
    `reward_money` decimal(10,2) default 0,
    `jiazhong_order_num` int default 0,
    `jiazhong_achieve_money` decimal(10,2) default 0,
    `jiazhong_reward_money` decimal(10,2) default 0,
    `status` tinyint DEFAULT 0,
    `del` tinyint DEFAULT 1,
    `create_at` int,
    `update_at` int,
    `delete_at` int,
    primary key(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='经营结算记录表';

drop table if exists `jishi_lincec_mch`;
CREATE TABLE `jishi_lincec_mch`(
      `id` int NOT NULL AUTO_INCREMENT,
      `uniacid` int,
      `cate` TINYINT DEFAULT 1,
      `user_id` int,
      `name` VARCHAR(32),
      `pic` VARCHAR(300),
      `tel` VARCHAR(20),
      `desc` VARCHAR(1000),
      `detail` text,
      `province` varchar(30),
      `city` varchar(30),
      `district` varchar(30),
      `address` varchar(200),
      `latitude` varchar(30),
      `longitude` varchar(30),
      `certificate` varchar(1000),
      `qrcode` varchar(100),
      `qrcode_gzh` varchar(300),
      `base_review` int DEFAULT 0,
      `review` int DEFAULT 0,
      `base_collect` int DEFAULT 0,
      `collect` int DEFAULT 0,
      `_sort` int DEFAULT NULL,
      `start_time` int,
      `end_time` int,
      `score` decimal(3,1) default 5.0,
      `status` TINYINT DEFAULT 1,
      `is_recommend` TINYINT DEFAULT 1,
      `is_excellent` TINYINT DEFAULT 0,
      `is_hot` TINYINT DEFAULT 0,
      `open_status` TINYINT DEFAULT 0,
      `del` TINYINT DEFAULT 1,
      `delete_at` int DEFAULT NULL,
      `create_at` int DEFAULT NULL,
      `update_at` int DEFAULT NULL,
      PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='商户表';

