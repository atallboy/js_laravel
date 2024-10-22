-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2024-08-02 15:36:58
-- 服务器版本： 5.6.50-log
-- PHP Version: 7.2.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jsdjys_jiuzhou`
--

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_access_token`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_access_token` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `app_id` varchar(32) DEFAULT NULL,
    `access_token` varchar(300) DEFAULT NULL,
    `invalid_time` int(11) DEFAULT NULL,
    `status` tinyint(4) DEFAULT '1',
    `del` tinyint(4) DEFAULT '1',
    `create_at` int(11) DEFAULT NULL,
    `delete_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_address`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_address` (
    `id` int(10) unsigned NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `name` varchar(30) DEFAULT NULL,
    `tel` varchar(30) DEFAULT NULL,
    `province` varchar(30) DEFAULT NULL,
    `city` varchar(30) DEFAULT NULL,
    `district` varchar(30) DEFAULT NULL,
    `address` varchar(200) DEFAULT NULL,
    `remark` varchar(100) DEFAULT NULL,
    `latitude` varchar(30) DEFAULT NULL,
    `longitude` varchar(30) DEFAULT NULL,
    `is_default` tinyint(4) DEFAULT '0',
    `status` tinyint(4) DEFAULT '1',
    `del` tinyint(4) DEFAULT '1',
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL,
    `delete_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='地址表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_admin`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_admin` (
    `id` int(6) unsigned NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `role_id` int(11) DEFAULT NULL,
    `token` varchar(32) DEFAULT NULL,
    `account` varchar(32) DEFAULT NULL,
    `password` varchar(32) DEFAULT NULL,
    `status` tinyint(4) DEFAULT '0',
    `del` tinyint(4) DEFAULT '1',
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL,
    `delete_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';
INSERT INTO jishi_lincec_admin (id,uniacid,role_id,token,account,password) VALUES (1,1,1,'a38c4a5d7c576da8a63db757570555f7','admin','838712f42c8d7874a30fae71e8bb0ade');
-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_agent`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_agent` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `name` varchar(30) DEFAULT NULL,
    `tel` varchar(30) DEFAULT NULL,
    `province` varchar(30) DEFAULT NULL,
    `city` varchar(30) DEFAULT NULL,
    `district` varchar(30) DEFAULT NULL,
    `remark` varchar(100) DEFAULT NULL,
    `status` tinyint(4) DEFAULT '0',
    `del` tinyint(4) DEFAULT '1',
    `delete_at` int(11) DEFAULT NULL,
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL,
    `order_num` int(11) NOT NULL DEFAULT '0',
    `order_money` decimal(10,2) NOT NULL DEFAULT '0.00'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='代理表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_balance_record`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_balance_record` (
    `id` int(6) unsigned NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `type` tinyint(4) DEFAULT '1' COMMENT '1加2减',
    `cate` tinyint(4) DEFAULT NULL,
    `_id` int(11) DEFAULT '0',
    `money` decimal(10,2) DEFAULT NULL,
    `before_balance` decimal(10,2) DEFAULT NULL,
    `after_balance` decimal(10,2) DEFAULT NULL,
    `remark` varchar(100) DEFAULT NULL,
    `del` tinyint(4) DEFAULT '1',
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL,
    `delete_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='余额表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_banner`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_banner` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `cate` tinyint(4) DEFAULT '1' COMMENT '1banner,2toast',
    `name` varchar(100) DEFAULT NULL,
    `pic` varchar(300) DEFAULT NULL,
    `type` tinyint(4) DEFAULT '1' COMMENT '0无跳转1跳转内部2跳转外部网页',
    `url` varchar(30) DEFAULT NULL,
    `param` varchar(30) DEFAULT NULL,
    `_sort` int(11) DEFAULT NULL,
    `status` tinyint(4) DEFAULT '1',
    `del` tinyint(4) DEFAULT '1',
    `create_at` int(11) DEFAULT NULL,
    `delete_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='轮播图表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_coupon`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_coupon` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `name` varchar(30) DEFAULT NULL,
    `get_type` tinyint(4) DEFAULT '1' COMMENT '1直接领取2兑换码领取3分享领取',
    `use_range` tinyint(4) DEFAULT '1' COMMENT '使用类目：1全场2指定分类3指定产品',
    `type` tinyint(4) DEFAULT '1' COMMENT '1直减2满减',
    `amount` decimal(10,2) DEFAULT NULL COMMENT '优惠券面值',
    `minimum` decimal(10,2) DEFAULT NULL COMMENT '满减门槛',
    `valid_time_type` tinyint(4) DEFAULT '1' COMMENT '有效时间方式：1固定期限2领取后计算',
    `valid_day` int(11) DEFAULT NULL COMMENT '有效天数',
    `valid_start_time` int(11) DEFAULT NULL COMMENT '有效开始时间',
    `valid_end_time` int(11) DEFAULT NULL COMMENT '有效结束时间',
    `num` int(11) DEFAULT NULL COMMENT '数量',
    `get_limit` int(11) DEFAULT '1' COMMENT '每人可领取数',
    `redeem_code` varchar(20) DEFAULT NULL COMMENT '兑换码',
    `_sort` int(11) DEFAULT NULL,
    `status` tinyint(4) DEFAULT '1',
    `del` tinyint(4) DEFAULT '1',
    `create_at` int(11) DEFAULT NULL,
    `delete_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='优惠券表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_coupon_exchange_record`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_coupon_exchange_record` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `coupon_id` int(11) DEFAULT NULL,
    `redeem_code` varchar(20) DEFAULT NULL,
    `status` tinyint(4) DEFAULT '1',
    `del` tinyint(4) DEFAULT '1',
    `create_at` int(11) DEFAULT NULL,
    `delete_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='优惠券兑换表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_coupon_record`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_coupon_record` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `cate` tinyint(4) DEFAULT '1' COMMENT '1兑换码领取2邀请奖励',
    `user_id` int(11) DEFAULT NULL,
    `coupon_id` int(11) DEFAULT NULL,
    `valid_start_time` int(11) DEFAULT NULL,
    `valid_end_time` int(11) DEFAULT NULL,
    `remark` varchar(100) DEFAULT NULL,
    `status` tinyint(4) DEFAULT '1',
    `del` tinyint(4) DEFAULT '1',
    `create_at` int(11) DEFAULT NULL,
    `delete_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='优惠券记录表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_eva_record`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_eva_record` (
    `id` int(6) unsigned NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `cate` tinyint(4) DEFAULT '1' COMMENT '1技师评价2服务项目评价',
    `_id` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `score` decimal(2,1) DEFAULT NULL,
    `tag` varchar(1000) DEFAULT NULL,
    `content` varchar(1000) DEFAULT NULL,
    `status` tinyint(4) DEFAULT '0',
    `del` tinyint(4) DEFAULT '1',
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL,
    `delete_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='评价表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_item`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_item` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `cate_id` int(11) DEFAULT '0',
    `name` varchar(32) DEFAULT NULL,
    `price` decimal(10,2) DEFAULT NULL,
    `old_price` decimal(10,2) DEFAULT NULL,
    `pic` varchar(300) DEFAULT NULL,
    `long_time` varchar(32) DEFAULT NULL,
    `desc` varchar(20) DEFAULT NULL,
    `tag` varchar(100) DEFAULT NULL,
    `range_people` varchar(32) DEFAULT NULL,
    `gender` tinyint(4) DEFAULT '1',
    `detail` text,
    `base_sale` int(11) DEFAULT '0',
    `real_sale` int(11) DEFAULT '0',
    `base_collect` int(11) DEFAULT '0',
    `real_collect` int(11) DEFAULT '0',
    `_sort` int(11) DEFAULT NULL,
    `status` tinyint(4) DEFAULT '1',
    `del` tinyint(4) DEFAULT '1',
    `delete_at` int(11) DEFAULT NULL,
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='服务项目表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_jiazhong_record`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_jiazhong_record` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `order_id` int(11) DEFAULT NULL,
    `product_money` decimal(10,2) DEFAULT '0.00',
    `status` tinyint(4) DEFAULT '0',
    `del` tinyint(4) DEFAULT '1',
    `delete_at` int(11) DEFAULT NULL,
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='加钟记录表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_log`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_log` (
    `id` int(6) unsigned NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `cate` tinyint(4) DEFAULT '1',
    `_id` int(11) DEFAULT NULL,
    `remark` varchar(300) DEFAULT NULL,
    `content` varchar(3000) DEFAULT NULL,
    `del` tinyint(4) DEFAULT '1',
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL,
    `delete_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='日志表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_master`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_master` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `cate` tinyint(4) DEFAULT '1',
    `user_id` int(11) DEFAULT NULL,
    `name` varchar(32) DEFAULT NULL,
    `weidu` varchar(255) DEFAULT NULL,
    `jindu` varchar(255) DEFAULT NULL,
    `locationName` varchar(1000) DEFAULT NULL,
    `pic` varchar(300) DEFAULT NULL,
    `tel` varchar(20) DEFAULT NULL,
    `desc` varchar(1000) DEFAULT NULL,
    `province` varchar(30) DEFAULT NULL,
    `city` varchar(30) DEFAULT NULL,
    `district` varchar(30) DEFAULT NULL,
    `address` varchar(200) DEFAULT NULL,
    `age` varchar(4) DEFAULT '1',
    `gender` tinyint(4) DEFAULT '1',
    `latitude` varchar(30) DEFAULT NULL,
    `longitude` varchar(30) DEFAULT NULL,
    `store_name` varchar(32) DEFAULT NULL,
    `certificate` varchar(1000) DEFAULT NULL,
    `qrcode` varchar(300) DEFAULT NULL,
    `qrcode_gzh` varchar(300) DEFAULT NULL,
    `order` int(11) DEFAULT '0',
    `base_review` int(11) DEFAULT '0',
    `review` int(11) DEFAULT '0',
    `base_collect` int(11) DEFAULT '0',
    `collect` int(11) DEFAULT '0',
    `_sort` int(11) DEFAULT NULL,
    `start_time` int(11) DEFAULT NULL,
    `end_time` int(11) DEFAULT NULL,
    `travel_expense` tinyint(4) DEFAULT '0',
    `taxi_fee` decimal(10,2) DEFAULT '0.00',
    `bus_fee` decimal(10,2) DEFAULT '0.00',
    `jiazhonglv` tinyint(4) DEFAULT '0',
    `qibujia` decimal(10,2) NOT NULL DEFAULT '0.00',
    `complete_order` int(11) DEFAULT '0',
    `jiazhong_order` int(11) NOT NULL DEFAULT '0',
    `eva_order` int(11) DEFAULT '0',
    `half_year_complete_order` int(11) DEFAULT '0',
    `score` decimal(3,1) DEFAULT '5.0',
    `status` tinyint(4) DEFAULT '1',
    `is_recommend` tinyint(4) DEFAULT '1',
    `is_excellent` tinyint(4) DEFAULT '0',
    `is_hot` tinyint(4) DEFAULT '0',
    `is_fast` tinyint(4) DEFAULT '0',
    `open_status` tinyint(4) DEFAULT '1',
    `del` tinyint(4) DEFAULT '1',
    `delete_at` int(11) DEFAULT NULL,
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='技师表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_master_collect`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_master_collect` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `master_id` int(11) DEFAULT NULL,
    `del` tinyint(4) DEFAULT '1',
    `delete_at` int(11) DEFAULT NULL,
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='技师收藏表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_mch`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_mch` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `cate` tinyint(4) DEFAULT '1',
    `user_id` int(11) DEFAULT NULL,
    `name` varchar(32) DEFAULT NULL,
    `pic` varchar(300) DEFAULT NULL,
    `tel` varchar(20) DEFAULT NULL,
    `desc` varchar(1000) DEFAULT NULL,
    `detail` text,
    `province` varchar(30) DEFAULT NULL,
    `city` varchar(30) DEFAULT NULL,
    `district` varchar(30) DEFAULT NULL,
    `address` varchar(200) DEFAULT NULL,
    `latitude` varchar(30) DEFAULT NULL,
    `longitude` varchar(30) DEFAULT NULL,
    `certificate` varchar(1000) DEFAULT NULL,
    `qrcode` varchar(100) DEFAULT NULL,
    `qrcode_gzh` varchar(300) DEFAULT NULL,
    `base_review` int(11) DEFAULT '0',
    `review` int(11) DEFAULT '0',
    `base_collect` int(11) DEFAULT '0',
    `collect` int(11) DEFAULT '0',
    `_sort` int(11) DEFAULT NULL,
    `start_time` int(11) DEFAULT NULL,
    `end_time` int(11) DEFAULT NULL,
    `score` decimal(3,1) DEFAULT '5.0',
    `status` tinyint(4) DEFAULT '1',
    `is_recommend` tinyint(4) DEFAULT '1',
    `is_excellent` tinyint(4) DEFAULT '0',
    `is_hot` tinyint(4) DEFAULT '0',
    `open_status` tinyint(4) DEFAULT '0',
    `del` tinyint(4) DEFAULT '1',
    `delete_at` int(11) DEFAULT NULL,
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_order`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_order` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `address_id` int(11) DEFAULT NULL,
    `master_id` int(11) DEFAULT NULL,
    `is_jiazhong` tinyint(2) NOT NULL DEFAULT '0',
    `pay_type` tinyint(4) DEFAULT '0',
    `total_fee` decimal(10,2) DEFAULT '0.00',
    `pay_fee` decimal(10,2) DEFAULT '0.00',
    `product_fee` decimal(10,2) DEFAULT '0.00',
    `coupon_id` int(11) DEFAULT NULL,
    `coupon_fee` decimal(10,2) DEFAULT '0.00',
    `travel_type` tinyint(4) DEFAULT '0' COMMENT '1出租2公交',
    `travel_fee` decimal(10,2) DEFAULT '0.00',
    `remark` varchar(100) DEFAULT NULL,
    `service_time` varchar(50) DEFAULT NULL,
    `snap_name` varchar(30) DEFAULT NULL,
    `snap_pic` varchar(300) DEFAULT NULL,
    `status` tinyint(4) DEFAULT '0',
    `del` tinyint(4) DEFAULT '1',
    `delete_at` int(11) DEFAULT NULL,
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL,
    `complete_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_order_detail`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_order_detail` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `order_id` int(11) DEFAULT NULL,
    `name` varchar(30) DEFAULT NULL,
    `tel` varchar(30) DEFAULT NULL,
    `province` varchar(30) DEFAULT NULL,
    `city` varchar(30) DEFAULT NULL,
    `district` varchar(30) DEFAULT NULL,
    `address` varchar(200) DEFAULT NULL,
    `remark` varchar(100) DEFAULT NULL,
    `latitude` varchar(30) DEFAULT NULL,
    `longitude` varchar(30) DEFAULT NULL,
    `del` tinyint(4) DEFAULT '1',
    `delete_at` int(11) DEFAULT NULL,
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单详情表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_order_product`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_order_product` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `order_id` int(11) DEFAULT NULL,
    `item_id` int(11) DEFAULT NULL,
    `num` int(11) DEFAULT NULL,
    `total_price` decimal(10,2) DEFAULT '0.00',
    `status` tinyint(4) DEFAULT '0',
    `del` tinyint(4) DEFAULT '1',
    `delete_at` int(11) DEFAULT NULL,
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单产品表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_order_refund`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_order_refund` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `order_id` int(11) DEFAULT NULL,
    `admin_id` int(11) DEFAULT '0',
    `order_no` varchar(100) DEFAULT NULL,
    `refund_no` varchar(100) DEFAULT NULL,
    `transaction_id` varchar(100) DEFAULT NULL,
    `reason` varchar(100) DEFAULT NULL,
    `remark` varchar(100) DEFAULT NULL,
    `refund_fee` decimal(10,2) DEFAULT '0.00',
    `result` varchar(1000) DEFAULT NULL,
    `status` tinyint(4) DEFAULT '0' COMMENT '0待处理1同意2拒绝3失败',
    `del` tinyint(4) DEFAULT '1',
    `delete_at` int(11) DEFAULT NULL,
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单退款表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_order_settle`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_order_settle` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `order_id` int(11) DEFAULT NULL,
    `platform_fee` decimal(10,2) DEFAULT '0.00',
    `master_fee` decimal(10,2) DEFAULT '0.00',
    `agent_fee` decimal(10,2) DEFAULT '0.00',
    `agent_avg_fee` decimal(10,2) DEFAULT '0.00',
    `del` tinyint(4) DEFAULT '1',
    `delete_at` int(11) DEFAULT NULL,
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单结算表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_paylog`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_paylog` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `cate` tinyint(4) DEFAULT '0' COMMENT '1服务订单2充值订单',
    `fee` decimal(10,2) DEFAULT '0.00',
    `pay_fee` decimal(10,2) DEFAULT '0.00',
    `status` tinyint(4) DEFAULT '0',
    `order_no` varchar(32) DEFAULT NULL,
    `out_trade_no` varchar(32) DEFAULT NULL,
    `remark` varchar(100) DEFAULT NULL,
    `notify_result` text,
    `del` tinyint(4) DEFAULT '1',
    `delete_at` int(11) DEFAULT NULL,
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='支付表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_recharge_record`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_recharge_record` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `recharge_id` int(11) DEFAULT NULL,
    `money` decimal(10,2) DEFAULT '0.00',
    `status` tinyint(4) DEFAULT '0',
    `del` tinyint(4) DEFAULT '1',
    `delete_at` int(11) DEFAULT NULL,
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='充值记录表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_setting`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_setting` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `name` varchar(30) DEFAULT NULL,
    `logo` varchar(300) DEFAULT NULL,
    `tel` varchar(300) DEFAULT NULL,
    `desc` text,
    `app_id` varchar(32) DEFAULT NULL,
    `app_secret` varchar(32) DEFAULT NULL,
    `mch_id` varchar(32) DEFAULT NULL,
    `mch_secret` varchar(32) DEFAULT NULL,
    `mch_cert_pem` text,
    `mch_key_pem` text,
    `gzh_appid` varchar(32) DEFAULT NULL,
    `gzh_appsecret` varchar(32) DEFAULT NULL,
    `master_percent` tinyint(4) DEFAULT NULL,
    `agent_percent` tinyint(4) DEFAULT NULL,
    `distribute_coupon` int(11) DEFAULT NULL,
    `eva_tag` varchar(1000) DEFAULT NULL,
    `open_service_link` tinyint(2) DEFAULT NULL DEFAULT '0',
    `service_link` varchar(300) DEFAULT NULL,
    `access_time` int(11) DEFAULT NULL,
    `access_token` varchar(300) DEFAULT NULL,
    `status` tinyint(4) DEFAULT '1',
    `del` tinyint(4) DEFAULT '1',
    `create_at` int(11) DEFAULT NULL,
    `delete_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL,
    `city_arr` varchar(3000) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统设置表';
INSERT INTO jishi_lincec_setting (id,uniacid) VALUES (1,1);

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_settle_record`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_settle_record` (
    `id` int(6) unsigned NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `settle_time_id` int(11) DEFAULT NULL,
    `order_num` int(11) DEFAULT '0',
    `achieve_money` decimal(10,2) DEFAULT '0.00',
    `reward_money` decimal(10,2) DEFAULT '0.00',
    `jiazhong_order_num` int(11) DEFAULT '0',
    `jiazhong_achieve_money` decimal(10,2) DEFAULT '0.00',
    `jiazhong_reward_money` decimal(10,2) DEFAULT '0.00',
    `status` tinyint(4) DEFAULT '0',
    `del` tinyint(4) DEFAULT '1',
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL,
    `delete_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='经营结算记录表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_settle_solution`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_settle_solution` (
    `id` int(6) unsigned NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `name` varchar(100) DEFAULT NULL,
    `settle_object` tinyint(4) DEFAULT '1' COMMENT '结算对象：1技师结算2代理结算',
    `settle_time` tinyint(4) DEFAULT '1' COMMENT '1每日2每周3每月4每季度5每年',
    `settle_type` tinyint(4) DEFAULT '1' COMMENT '1手动结算2自动结算',
    `master_id` int(11) DEFAULT NULL,
    `agent_id` int(11) DEFAULT NULL,
    `status` tinyint(4) DEFAULT '0',
    `del` tinyint(4) DEFAULT '1',
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL,
    `delete_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='经营结算方案表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_settle_solution_ladder`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_settle_solution_ladder` (
    `id` int(6) unsigned NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `settle_solution_id` int(11) DEFAULT NULL,
    `min` int(11) DEFAULT NULL,
    `max` int(11) DEFAULT NULL,
    `fee_type` tinyint(4) NOT NULL DEFAULT '1',
    `calc_type` tinyint(4) DEFAULT '1' COMMENT '1百分比2固定金额',
    `percent` tinyint(4) DEFAULT '0',
    `status` tinyint(4) DEFAULT '0',
    `del` tinyint(4) DEFAULT '1',
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL,
    `delete_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='经营结算方案阶级表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_settle_time`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_settle_time` (
    `id` int(6) unsigned NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `settle_solution_id` int(11) DEFAULT NULL,
    `_time` varchar(20) DEFAULT NULL,
    `start_time` varchar(10) DEFAULT NULL,
    `end_time` varchar(10) DEFAULT NULL,
    `del` tinyint(4) DEFAULT '1',
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL,
    `delete_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='结算时间记录表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_site`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_site` (
    `id` int(11) NOT NULL,
    `name` varchar(32) DEFAULT NULL,
    `desc` varchar(100) DEFAULT NULL,
    `status` tinyint(4) DEFAULT '1',
    `del` tinyint(4) DEFAULT '1',
    `create_at` int(11) DEFAULT NULL,
    `delete_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='站点表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_subscribe_message`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_subscribe_message` (
    `id` int(6) unsigned NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `trigger_event` int(11) DEFAULT NULL COMMENT '触发事件',
    `name` varchar(100) DEFAULT NULL,
    `template_id` varchar(100) DEFAULT NULL,
    `form` varchar(3000) DEFAULT NULL,
    `redirect_url` varchar(300) DEFAULT NULL,
    `status` tinyint(4) DEFAULT '1',
    `del` tinyint(4) DEFAULT '1',
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL,
    `delete_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订阅消息配置表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_suggest`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_suggest` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `content` varchar(100) DEFAULT NULL,
    `pic` varchar(3000) DEFAULT NULL,
    `remark` varchar(300) DEFAULT NULL,
    `status` tinyint(4) DEFAULT '0',
    `del` tinyint(4) DEFAULT '1',
    `delete_at` int(11) DEFAULT NULL,
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='投诉建议表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_user`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_user` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `pre_id` int(11) DEFAULT NULL,
    `token` varchar(32) DEFAULT NULL,
    `openid` varchar(50) NOT NULL,
    `session_key` varchar(50) NOT NULL,
    `unionid` varchar(50) NOT NULL,
    `nickName` varchar(128) DEFAULT NULL,
    `avatarUrl` varchar(300) DEFAULT NULL,
    `total_balance` decimal(10,2) DEFAULT '0.00',
    `balance` decimal(10,2) DEFAULT '0.00',
    `withdrawal` decimal(10,2) NOT NULL,
    `integral` int(11) DEFAULT '0',
    `tel` varchar(30) DEFAULT NULL,
    `country` varchar(50) DEFAULT NULL,
    `province` varchar(50) DEFAULT NULL,
    `city` varchar(50) DEFAULT NULL,
    `gender` tinyint(4) DEFAULT NULL,
    `qrcode` varchar(300) DEFAULT NULL,
    `qrcode_gzh` varchar(300) DEFAULT NULL,
    `status` tinyint(4) DEFAULT '1',
    `del` tinyint(4) DEFAULT '1',
    `delete_at` int(11) DEFAULT NULL,
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL,
    `recent_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户信息表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_user_gzh`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_user_gzh` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `openid` varchar(50) DEFAULT NULL,
    `unionid` varchar(50) DEFAULT NULL,
    `subscribe_scene` varchar(50) DEFAULT NULL,
    `subscribe` tinyint(4) DEFAULT NULL,
    `subscribe_time` int(11) DEFAULT NULL,
    `sex` tinyint(4) DEFAULT NULL,
    `del` tinyint(4) DEFAULT '1',
    `create_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公众号用户表';

-- --------------------------------------------------------

--
-- 表的结构 `jishi_lincec_withdrawal_record`
--

CREATE TABLE IF NOT EXISTS `jishi_lincec_withdrawal_record` (
    `id` int(6) unsigned NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `money` decimal(10,2) DEFAULT NULL,
    `type` tinyint(4) DEFAULT '1' COMMENT '1支付宝',
    `name` varchar(100) DEFAULT NULL,
    `tel` varchar(100) DEFAULT NULL,
    `zfb_account` varchar(100) DEFAULT NULL,
    `remark` varchar(100) DEFAULT NULL,
    `status` tinyint(4) DEFAULT '0',
    `del` tinyint(4) DEFAULT '1',
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL,
    `delete_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='提现表';

-- --------------------------------------------------------

--
-- 表的结构 `nuoche_lincec_admin`
--

CREATE TABLE IF NOT EXISTS `nuoche_lincec_admin` (
    `id` int(6) unsigned NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `role_id` int(11) DEFAULT NULL,
    `token` varchar(32) DEFAULT NULL,
    `account` varchar(32) DEFAULT NULL,
    `password` varchar(32) DEFAULT NULL,
    `status` tinyint(4) DEFAULT '0',
    `del` tinyint(4) DEFAULT '1',
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL,
    `delete_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';
INSERT INTO jishi_lincec_admin (uniacid,role_id,token,account,password,status) VALUES (1,1,'a38c4a5d7c576da8a63db757570555f7','i77','a38c4a5d7c576da8a63db757570555f7',1);

-- --------------------------------------------------------

--
-- 表的结构 `nuoche_lincec_agent`
--

CREATE TABLE IF NOT EXISTS `nuoche_lincec_agent` (
    `id` int(11) NOT NULL,
    `uniacid` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `name` varchar(30) DEFAULT NULL,
    `tel` varchar(30) DEFAULT NULL,
    `province` varchar(30) DEFAULT NULL,
    `city` varchar(30) DEFAULT NULL,
    `district` varchar(30) DEFAULT NULL,
    `remark` varchar(100) DEFAULT NULL,
    `status` tinyint(4) DEFAULT '0',
    `del` tinyint(4) DEFAULT '1',
    `delete_at` int(11) DEFAULT NULL,
    `create_at` int(11) DEFAULT NULL,
    `update_at` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='代理表';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jishi_lincec_access_token`
--
ALTER TABLE `jishi_lincec_access_token`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_address`
--
ALTER TABLE `jishi_lincec_address`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_admin`
--
ALTER TABLE `jishi_lincec_admin`
    ADD PRIMARY KEY (`id`),
  ADD KEY `uniacid` (`uniacid`);

--
-- Indexes for table `jishi_lincec_agent`
--
ALTER TABLE `jishi_lincec_agent`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_balance_record`
--
ALTER TABLE `jishi_lincec_balance_record`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_banner`
--
ALTER TABLE `jishi_lincec_banner`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_coupon`
--
ALTER TABLE `jishi_lincec_coupon`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_coupon_exchange_record`
--
ALTER TABLE `jishi_lincec_coupon_exchange_record`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_coupon_record`
--
ALTER TABLE `jishi_lincec_coupon_record`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_eva_record`
--
ALTER TABLE `jishi_lincec_eva_record`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_item`
--
ALTER TABLE `jishi_lincec_item`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_jiazhong_record`
--
ALTER TABLE `jishi_lincec_jiazhong_record`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_log`
--
ALTER TABLE `jishi_lincec_log`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_master`
--
ALTER TABLE `jishi_lincec_master`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_master_collect`
--
ALTER TABLE `jishi_lincec_master_collect`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_mch`
--
ALTER TABLE `jishi_lincec_mch`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_order`
--
ALTER TABLE `jishi_lincec_order`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_order_detail`
--
ALTER TABLE `jishi_lincec_order_detail`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_order_product`
--
ALTER TABLE `jishi_lincec_order_product`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_order_refund`
--
ALTER TABLE `jishi_lincec_order_refund`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_order_settle`
--
ALTER TABLE `jishi_lincec_order_settle`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_paylog`
--
ALTER TABLE `jishi_lincec_paylog`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_recharge_record`
--
ALTER TABLE `jishi_lincec_recharge_record`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_setting`
--
ALTER TABLE `jishi_lincec_setting`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_settle_record`
--
ALTER TABLE `jishi_lincec_settle_record`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_settle_solution`
--
ALTER TABLE `jishi_lincec_settle_solution`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_settle_solution_ladder`
--
ALTER TABLE `jishi_lincec_settle_solution_ladder`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_settle_time`
--
ALTER TABLE `jishi_lincec_settle_time`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_site`
--
ALTER TABLE `jishi_lincec_site`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_subscribe_message`
--
ALTER TABLE `jishi_lincec_subscribe_message`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_suggest`
--
ALTER TABLE `jishi_lincec_suggest`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_user`
--
ALTER TABLE `jishi_lincec_user`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jishi_lincec_user_gzh`
--
ALTER TABLE `jishi_lincec_user_gzh`
    ADD PRIMARY KEY (`id`),
  ADD KEY `openid` (`openid`),
  ADD KEY `unionid` (`unionid`);

--
-- Indexes for table `jishi_lincec_withdrawal_record`
--
ALTER TABLE `jishi_lincec_withdrawal_record`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nuoche_lincec_admin`
--
ALTER TABLE `nuoche_lincec_admin`
    ADD PRIMARY KEY (`id`),
  ADD KEY `uniacid` (`uniacid`);

--
-- Indexes for table `nuoche_lincec_agent`
--
ALTER TABLE `nuoche_lincec_agent`
    ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jishi_lincec_access_token`
--
ALTER TABLE `jishi_lincec_access_token`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_address`
--
ALTER TABLE `jishi_lincec_address`
    MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_admin`
--
ALTER TABLE `jishi_lincec_admin`
    MODIFY `id` int(6) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_agent`
--
ALTER TABLE `jishi_lincec_agent`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_balance_record`
--
ALTER TABLE `jishi_lincec_balance_record`
    MODIFY `id` int(6) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_banner`
--
ALTER TABLE `jishi_lincec_banner`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_coupon`
--
ALTER TABLE `jishi_lincec_coupon`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_coupon_exchange_record`
--
ALTER TABLE `jishi_lincec_coupon_exchange_record`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_coupon_record`
--
ALTER TABLE `jishi_lincec_coupon_record`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_eva_record`
--
ALTER TABLE `jishi_lincec_eva_record`
    MODIFY `id` int(6) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_item`
--
ALTER TABLE `jishi_lincec_item`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_jiazhong_record`
--
ALTER TABLE `jishi_lincec_jiazhong_record`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_log`
--
ALTER TABLE `jishi_lincec_log`
    MODIFY `id` int(6) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_master`
--
ALTER TABLE `jishi_lincec_master`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_master_collect`
--
ALTER TABLE `jishi_lincec_master_collect`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_mch`
--
ALTER TABLE `jishi_lincec_mch`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_order`
--
ALTER TABLE `jishi_lincec_order`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_order_detail`
--
ALTER TABLE `jishi_lincec_order_detail`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_order_product`
--
ALTER TABLE `jishi_lincec_order_product`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_order_refund`
--
ALTER TABLE `jishi_lincec_order_refund`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_order_settle`
--
ALTER TABLE `jishi_lincec_order_settle`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_paylog`
--
ALTER TABLE `jishi_lincec_paylog`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_recharge_record`
--
ALTER TABLE `jishi_lincec_recharge_record`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_setting`
--
ALTER TABLE `jishi_lincec_setting`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_settle_record`
--
ALTER TABLE `jishi_lincec_settle_record`
    MODIFY `id` int(6) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_settle_solution`
--
ALTER TABLE `jishi_lincec_settle_solution`
    MODIFY `id` int(6) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_settle_solution_ladder`
--
ALTER TABLE `jishi_lincec_settle_solution_ladder`
    MODIFY `id` int(6) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_settle_time`
--
ALTER TABLE `jishi_lincec_settle_time`
    MODIFY `id` int(6) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_site`
--
ALTER TABLE `jishi_lincec_site`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_subscribe_message`
--
ALTER TABLE `jishi_lincec_subscribe_message`
    MODIFY `id` int(6) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_suggest`
--
ALTER TABLE `jishi_lincec_suggest`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_user`
--
ALTER TABLE `jishi_lincec_user`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_user_gzh`
--
ALTER TABLE `jishi_lincec_user_gzh`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jishi_lincec_withdrawal_record`
--
ALTER TABLE `jishi_lincec_withdrawal_record`
    MODIFY `id` int(6) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `nuoche_lincec_admin`
--
ALTER TABLE `nuoche_lincec_admin`
    MODIFY `id` int(6) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `nuoche_lincec_agent`
--
ALTER TABLE `nuoche_lincec_agent`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
