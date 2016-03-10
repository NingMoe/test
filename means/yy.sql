-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2016 年 01 月 08 日 16:52
-- 服务器版本: 5.5.40
-- PHP 版本: 5.3.29

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `yy`
--

-- --------------------------------------------------------

--
-- 表的结构 `yy_address`
--

CREATE TABLE IF NOT EXISTS `yy_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属客户',
  `name` varchar(15) DEFAULT NULL COMMENT '收件姓名',
  `phone` varchar(11) DEFAULT NULL COMMENT '收件手机',
  `address` varchar(63) DEFAULT NULL COMMENT '详细地址',
  `province` varchar(15) DEFAULT NULL COMMENT '省',
  `city` varchar(15) DEFAULT NULL COMMENT '城市',
  `district` varchar(15) DEFAULT NULL COMMENT '区',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='客户保单收取地址' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `yy_admin`
--

CREATE TABLE IF NOT EXISTS `yy_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
  `account` varchar(32) DEFAULT NULL COMMENT '管理员账号',
  `password` varchar(36) DEFAULT NULL COMMENT '管理员密码',
  `mobile` varchar(11) DEFAULT NULL COMMENT '手机号',
  `login_time` int(11) DEFAULT NULL COMMENT '最后登录时间',
  `login_ip` varchar(15) DEFAULT NULL COMMENT '最后登录IP',
  `email` varchar(40) DEFAULT NULL COMMENT '邮箱',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '账户状态，禁用为0   启用为1',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `islogin` tinyint(1) unsigned DEFAULT '0' COMMENT '是否登录，登录为1，没有为0',
  `role` tinyint(1) unsigned DEFAULT NULL COMMENT '管理类型，1管理，2电话400,3销售客服',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;

--
-- 转存表中的数据 `yy_admin`
--

INSERT INTO `yy_admin` (`id`, `account`, `password`, `mobile`, `login_time`, `login_ip`, `email`, `status`, `create_time`, `islogin`, `role`) VALUES
(52, 'admin', '6f03cad59698da80635f8345467f7bbb', NULL, 1452224029, '2130706433', NULL, 1, 1450963213, 1, 1),
(56, 'service1', 'b923a8a417d59c68681434edd5ccc2a5', NULL, 1451269889, '2130706433', NULL, 1, 1451117235, 1, 3),
(57, 'service2', '5318999f95d8a6033b460613f596d3d5', NULL, 1451269853, '2130706433', NULL, 1, 1451117249, 1, 3),
(58, 'service3', '55843f3a98ebecd66859b06f9a957e8d', NULL, NULL, NULL, NULL, 1, 1451117257, 1, 3);

-- --------------------------------------------------------

--
-- 表的结构 `yy_auth_group`
--

CREATE TABLE IF NOT EXISTS `yy_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` char(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `yy_auth_group`
--

INSERT INTO `yy_auth_group` (`id`, `title`, `status`, `rules`) VALUES
(1, '管理员', 1, '1,2,3'),
(2, '销售客服', 1, '1'),
(3, '电话客服', 1, '');

-- --------------------------------------------------------

--
-- 表的结构 `yy_auth_group_access`
--

CREATE TABLE IF NOT EXISTS `yy_auth_group_access` (
  `uid` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `yy_auth_group_access`
--

INSERT INTO `yy_auth_group_access` (`uid`, `group_id`) VALUES
(52, 1),
(56, 2),
(57, 2),
(58, 2);

-- --------------------------------------------------------

--
-- 表的结构 `yy_auth_rule`
--

CREATE TABLE IF NOT EXISTS `yy_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(80) NOT NULL DEFAULT '',
  `title` char(20) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `condition` char(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `yy_auth_rule`
--

INSERT INTO `yy_auth_rule` (`id`, `name`, `title`, `type`, `status`, `condition`) VALUES
(1, 'admin/add', 'add user', 1, 1, ''),
(2, 'admin/delete', 'delete user', 1, 1, ''),
(3, 'admin/update', 'update user', 1, 1, '');

-- --------------------------------------------------------

--
-- 表的结构 `yy_client`
--

CREATE TABLE IF NOT EXISTS `yy_client` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` char(16) DEFAULT '' COMMENT '用户名',
  `password` char(32) NOT NULL COMMENT '密码',
  `email` char(32) DEFAULT '' COMMENT '用户邮箱',
  `mobile` char(15) NOT NULL DEFAULT '' COMMENT '用户手机',
  `driving_id` int(11) unsigned DEFAULT NULL COMMENT '当前行驶证id，可能有多个，在行驶证里找',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) DEFAULT '0' COMMENT '用户状态0默认1已传行驶2提交套餐',
  `service_id` int(11) DEFAULT NULL COMMENT '客服id',
  `red_usable` int(11) unsigned DEFAULT '0' COMMENT '红包可用金额',
  `red_total` int(11) unsigned DEFAULT '0' COMMENT '历史红包总金额',
  `share_string` varchar(15) DEFAULT NULL COMMENT '分享标识字符串',
  `nickname` varchar(15) DEFAULT NULL COMMENT '微信昵称',
  `wid` varchar(31) DEFAULT NULL COMMENT '微信ID',
  `province` varchar(15) DEFAULT NULL COMMENT '省份',
  `city` varchar(15) DEFAULT NULL COMMENT '城市',
  `openid` varchar(39) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mobile` (`mobile`),
  KEY `status` (`status`),
  KEY `email` (`email`),
  KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户表' AUTO_INCREMENT=10 ;

--
-- 转存表中的数据 `yy_client`
--

INSERT INTO `yy_client` (`id`, `username`, `password`, `email`, `mobile`, `driving_id`, `reg_time`, `reg_ip`, `last_login_time`, `last_login_ip`, `update_time`, `status`, `service_id`, `red_usable`, `red_total`, `share_string`, `nickname`, `wid`, `province`, `city`, `openid`) VALUES
(2, 'oui', 'e52f7132b0d6f492b5add661a5b4b864', '', '18739050431', 1, 1450240618, 0, 1452151805, 2130706433, 0, 0, 56, 0, 0, '160102515010294', NULL, NULL, NULL, NULL, NULL),
(4, 'newuser1', 'a0f04058edf718773865ed4a47620a2f', '', '13002100001', 4, 1451115763, 0, 1452233907, 460549214, 0, 0, 56, 0, 0, '', NULL, NULL, NULL, NULL, NULL),
(5, 'newuser2', 'a42481f2cd26a910dc31d4610e099c22', '', '13002100002', 5, 1451115784, 0, 1451274181, 2130706433, 0, 0, 57, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'newuser3', 'facc29bb8e09aca73bc14dc04b66c338', '', '13002100003', 7, 1451115801, 0, 1451547258, 2130706433, 0, 0, 57, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'newuser4', '293a62f1fd38468eee0d97d51b28972b', '', '13002100004', NULL, 1451115814, 0, 1451115814, 2130706433, 0, 0, 57, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'newuser5', '5f1423e6ab4044079248e6bf57ceb5b0', '', '13002100005', NULL, 1451115834, 0, 1451132657, 2130706433, 0, 0, 58, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(9, '', 'e764c48e2f400785d113a7136b578131', '', '18610011941', 6, 1451273144, 0, 1452153285, 1928526044, 0, 0, 56, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `yy_clientaction`
--

CREATE TABLE IF NOT EXISTS `yy_clientaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户id',
  `actiontype` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '行为类别',
  `value` varchar(31) DEFAULT NULL COMMENT '行为值',
  `createtime` int(11) unsigned DEFAULT NULL COMMENT '发生时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='客户行为记录' AUTO_INCREMENT=24 ;

--
-- 转存表中的数据 `yy_clientaction`
--

INSERT INTO `yy_clientaction` (`id`, `client_id`, `actiontype`, `value`, `createtime`) VALUES
(1, 2, 1, NULL, 1451886475),
(2, 2, 1, NULL, 1451896258),
(3, 2, 1, NULL, 1451960752),
(4, 2, 1, NULL, 1451961018),
(5, 2, 1, NULL, 1451961066),
(6, 2, 1, NULL, 1451961103),
(7, 2, 1, NULL, 1451961139),
(8, 2, 1, NULL, 1452052823),
(9, 2, 1, NULL, 1452088142),
(10, 4, 1, NULL, 1452132732),
(11, 4, 1, NULL, 1452134326),
(12, 4, 1, NULL, 1452135364),
(13, 2, 1, NULL, 1452151659),
(14, 2, 1, NULL, 1452151805),
(15, 9, 1, NULL, 1452153285),
(16, 4, 1, NULL, 1452188812),
(17, 4, 1, NULL, 1452188863),
(18, 4, 1, NULL, 1452232736),
(19, 4, 1, NULL, 1452232830),
(20, 4, 1, NULL, 1452232883),
(21, 4, 1, NULL, 1452232883),
(22, 4, 1, NULL, 1452232899),
(23, 4, 1, NULL, 1452233907);

-- --------------------------------------------------------

--
-- 表的结构 `yy_company`
--

CREATE TABLE IF NOT EXISTS `yy_company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(31) NOT NULL DEFAULT '' COMMENT '公司名称',
  `discount` float unsigned DEFAULT '1' COMMENT '折扣',
  `updatetime` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态-1时停用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='保险公司' AUTO_INCREMENT=8 ;

--
-- 转存表中的数据 `yy_company`
--

INSERT INTO `yy_company` (`id`, `name`, `discount`, `updatetime`, `status`) VALUES
(1, '太平洋保险', 0.475, 1451273519, 0),
(2, '平安车险', 1, 1451031346, 0),
(3, '人保车险', 0.6, 1451031388, -1),
(4, '新的公司', 0.63, 1451031829, 0),
(6, '新的公司2', 0.6, 1451032340, 0);

-- --------------------------------------------------------

--
-- 表的结构 `yy_config`
--

CREATE TABLE IF NOT EXISTS `yy_config` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(15) NOT NULL DEFAULT '' COMMENT '名称',
  `value` varchar(15) NOT NULL DEFAULT '' COMMENT '值',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='配置' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `yy_config`
--

INSERT INTO `yy_config` (`Id`, `key`, `value`) VALUES
(1, 'red_scale', '0.05');

-- --------------------------------------------------------

--
-- 表的结构 `yy_ctoken`
--

CREATE TABLE IF NOT EXISTS `yy_ctoken` (
  `appId` varchar(255) NOT NULL,
  `access_token` varchar(255) DEFAULT NULL,
  `expires_in` int(11) DEFAULT NULL,
  `addTimestamp` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `yy_ctoken`
--

INSERT INTO `yy_ctoken` (`appId`, `access_token`, `expires_in`, `addTimestamp`) VALUES
('wx4e5c5f23b9c7d35c', 'c6PbwFj3g5pq4DPXMjTUaLU61CtWEbh1MqB9gmgZ38DexgepTkVkokNRpgStreLRpfD8-aZBBkgvEFpCOnqnjhlw_9vREeCECWa8ke5n5Q0DPTaAHATFJ', 7200, 1451718510);

-- --------------------------------------------------------

--
-- 表的结构 `yy_drivinglicense`
--

CREATE TABLE IF NOT EXISTS `yy_drivinglicense` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属客户id',
  `cartype_id` tinyint(3) DEFAULT '0' COMMENT '车辆类型id，excel里的类型',
  `license_number` char(9) NOT NULL,
  `owner` varchar(20) NOT NULL DEFAULT '',
  `car_model` varchar(31) NOT NULL,
  `vin` char(17) NOT NULL,
  `engine_no` varchar(31) NOT NULL DEFAULT '' COMMENT '发动机号',
  `register_time` int(11) NOT NULL,
  `bar_code` char(13) NOT NULL DEFAULT '' COMMENT '副页条码',
  `first_id` int(11) unsigned DEFAULT NULL COMMENT '首页id',
  `second_id` int(11) unsigned DEFAULT NULL COMMENT '副页id',
  `package_id` int(11) unsigned DEFAULT NULL COMMENT '当前 保险套餐id',
  `last_package_id` int(11) unsigned DEFAULT NULL COMMENT '（去年套餐）最近一次投保套餐id，投保成功后把当前切换到这',
  `pricesheet_id` int(11) unsigned DEFAULT NULL COMMENT '原始报价单id',
  `pricesheet_count` int(10) unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态，0默认已上传，-1无效重新传，1投保成功，2投保过期',
  `action_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '客服处理各种行为状态',
  `car_year` tinyint(3) unsigned DEFAULT NULL COMMENT '车龄',
  `car_price` int(11) unsigned DEFAULT NULL COMMENT '车身价',
  `discount` float(5,4) unsigned DEFAULT NULL COMMENT '出险折扣',
  `jq_price` float(7,2) unsigned DEFAULT NULL COMMENT '交强险价',
  `cc_price` float(7,2) unsigned DEFAULT NULL COMMENT '车船税价',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '该记录更新时间',
  `package_style` tinyint(1) unsigned DEFAULT NULL COMMENT '投保套餐类型0diy,1lastyear,2helpme',
  `passenger_num` tinyint(1) unsigned DEFAULT NULL COMMENT '乘客座位数',
  `remark` varchar(511) DEFAULT NULL COMMENT '客服备注信息',
  `insure_companyid` tinyint(3) unsigned DEFAULT NULL COMMENT '最近投保的保险公司',
  `expire` int(11) unsigned DEFAULT NULL COMMENT '保险到期时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='行驶证' AUTO_INCREMENT=8 ;

--
-- 转存表中的数据 `yy_drivinglicense`
--

INSERT INTO `yy_drivinglicense` (`id`, `client_id`, `cartype_id`, `license_number`, `owner`, `car_model`, `vin`, `engine_no`, `register_time`, `bar_code`, `first_id`, `second_id`, `package_id`, `last_package_id`, `pricesheet_id`, `pricesheet_count`, `status`, `action_status`, `car_year`, `car_price`, `discount`, `jq_price`, `cc_price`, `update_time`, `package_style`, `passenger_num`, `remark`, `insure_companyid`, `expire`) VALUES
(1, 2, 2, '沪a12345', '王菲', '梅塞德斯奔驰WDDNG54X17A', 'WDDNG54X17A167127', '2729463070', 1262275200, '1234567890123', 27, 28, 2, NULL, 21, 12609, 0, 0, 2, 360021, 0.9600, 985.56, 1025.02, NULL, NULL, 4, NULL, NULL, NULL),
(4, 4, 3, '', '', '', '', '', 1360512000, '', 39, 40, 4, NULL, 4, 12836, 0, 0, 3, 600000, 0.9100, 650.30, 1024.50, 1451143010, NULL, NULL, NULL, NULL, NULL),
(5, 5, 2, '', '', '', '', '', 1325347200, '', 41, 42, 5, NULL, 5, 12336, 0, 0, 3, 300000, 0.9000, 700.25, 2010.25, 1451219726, NULL, NULL, NULL, NULL, NULL),
(6, 9, 1, '', '', '', '', '', 1317398400, '', 43, 44, 6, NULL, 6, 4028, 0, 0, 4, 70000, 0.9000, 950.00, 400.00, 1451273260, NULL, NULL, NULL, NULL, NULL),
(7, 6, 1, '', '', '', '', '', 1417449600, '', 45, 46, 7, NULL, 7, 6616, 0, 5, 1, 93000, 0.6650, 770.00, 720.00, 1451274673, NULL, 4, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `yy_e_cargroup`
--

CREATE TABLE IF NOT EXISTS `yy_e_cargroup` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` tinyint(3) unsigned DEFAULT '0' COMMENT '父分类id，本表内',
  `name` varchar(255) DEFAULT NULL COMMENT '分类名称',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='车类型分组' AUTO_INCREMENT=16 ;

--
-- 转存表中的数据 `yy_e_cargroup`
--

INSERT INTO `yy_e_cargroup` (`Id`, `pid`, `name`) VALUES
(1, 0, '家庭自用汽车与非营业用车'),
(2, 0, '营业用车与特种车'),
(3, 0, '摩托车与拖拉机'),
(4, 1, '家庭自用汽车'),
(5, 1, '企业\r\n非营业客车'),
(6, 1, '党政机关、事业团体\n非营业客车'),
(7, 1, '非营业货车'),
(9, 2, '出租、租赁'),
(10, 2, '城市公交\r\n营业客车'),
(11, 2, '公路客运\n营业客车'),
(12, 2, '营业货车'),
(13, 2, '特种车'),
(14, 3, '摩托车'),
(15, 3, '拖拉机'),
(8, 1, '非营业特种车');

-- --------------------------------------------------------

--
-- 表的结构 `yy_e_cartype`
--

CREATE TABLE IF NOT EXISTS `yy_e_cartype` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(31) DEFAULT NULL COMMENT '名称',
  `typegroup_id` tinyint(3) unsigned DEFAULT NULL COMMENT '类型分组id',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='车类型信息表' AUTO_INCREMENT=50 ;

--
-- 转存表中的数据 `yy_e_cartype`
--

INSERT INTO `yy_e_cartype` (`Id`, `name`, `typegroup_id`) VALUES
(1, '6座以下', 4),
(2, '6-10座', 4),
(3, '10座以上', 4),
(4, '6座以下', 5),
(5, '6-10座', 5),
(6, '10-20座', 5),
(7, '20座以上', 5),
(8, '6座以下', 6),
(9, '6-10座', 6),
(10, '10-20座', 6),
(11, '20座以上', 6),
(12, '2吨以下', 7),
(13, '2-5吨', 7),
(14, '5-10吨', 7),
(15, '10吨以上', 7),
(16, '低速载货汽车', 7),
(21, '6座以下', 9),
(22, '6-10座', 9),
(23, '10-20座', 9),
(24, '20－36座', 9),
(25, '36座以上', 9),
(26, '6-10座', 10),
(27, '10-20座', 10),
(28, '20－36座', 10),
(29, '36座以上', 10),
(30, '6-10座', 11),
(31, '10-20座', 11),
(32, '20－36座', 11),
(33, '36座以上', 11),
(34, '2吨以下', 12),
(35, '2-5吨', 12),
(36, '5-10吨', 12),
(37, '10吨以上', 12),
(38, '低速载货汽车', 12),
(39, '特种车型一', 13),
(40, '特种车型二', 13),
(41, '特种车型三', 13),
(42, '特种车型四', 13),
(43, '50CC及以下', 14),
(44, '50CC-250CC（含）', 14),
(45, '250CC以上及侧三轮', 14),
(46, '兼用型拖拉机14.7KW及以下', 15),
(47, '兼用型拖拉机14.7KW以上', 15),
(48, '运输型拖拉机14.7KW及以下', 15),
(49, '运输型拖拉机14.7KW以上', 15),
(17, '特种车型一', 8),
(18, '特种车型二', 8),
(19, '特种车型三', 8),
(20, '特种车型四', 8);

-- --------------------------------------------------------

--
-- 表的结构 `yy_order`
--

CREATE TABLE IF NOT EXISTS `yy_order` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `number` char(12) NOT NULL DEFAULT '' COMMENT '订单号码',
  `content` varchar(1790) DEFAULT NULL COMMENT '订单内容',
  `status` tinyint(1) DEFAULT '0' COMMENT '订单状态0已报价生空，-1过期失效，1有内容生成，2已支付',
  `amount` float(9,2) unsigned DEFAULT NULL COMMENT '订单折后最终原价',
  `pay_amount` int(9) unsigned DEFAULT NULL COMMENT '实付金额',
  `red_amount` smallint(5) unsigned DEFAULT NULL COMMENT '红包支付金额',
  `updatetime` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `paytime` int(11) unsigned DEFAULT NULL COMMENT '支付时间',
  `deadtime` int(11) unsigned DEFAULT '0' COMMENT '失效时间，同报价单的失效时间',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='订单表' AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `yy_order`
--

INSERT INTO `yy_order` (`Id`, `number`, `content`, `status`, `amount`, `pay_amount`, `red_amount`, `updatetime`, `paytime`, `deadtime`) VALUES
(2, '160105569849', '{"companyId":"1","company":"\\u592a\\u5e73\\u6d0b\\u4fdd\\u9669","allPrice":14619.58,"allPriceDis":6944.3005,"catePrice":"12609","catePriceDis":5989.275,"jq_price":"985.56","cc_price":"1025.02","cateList":{"dsfzr":{"name":"\\u7b2c\\u4e09\\u8005\\u8d23\\u4efb\\u9669","level":150,"price":2133.408,"disPrice":1013.3688,"noPayPrice":0,"disNoPayPrice":0},"clss":{"name":"\\u8f66\\u8f86\\u635f\\u5931\\u9669","level":"","price":5003.776032,"disPrice":2376.7936152,"noPayPrice":750.5664048,"disNoPayPrice":356.51904228},"qcdq":{"name":"\\u5168\\u8f66\\u76d7\\u62a2\\u9669","level":"","price":1194.486154752,"disPrice":567.3809235072,"noPayPrice":238.8972309504,"disNoPayPrice":113.47618470144},"sjzwzr":{"name":"\\u53f8\\u673a\\u4e58\\u5750\\u8d23\\u4efb\\u9669","level":5,"price":192,"disPrice":91.2,"noPayPrice":28.8,"disNoPayPrice":13.68},"ckzwzr":{"name":"\\u4e58\\u5ba2\\u5ea7\\u4f4d\\u8d23\\u4efb\\u9669","level":5,"price":499.2,"disPrice":237.12,"noPayPrice":0,"disNoPayPrice":0},"bl":{"name":"\\u73bb\\u7483\\u5355\\u72ec\\u7834\\u788e\\u9669","level":"","price":656.678304,"disPrice":311.9221944,"noPayPrice":null,"disNoPayPrice":0},"ss":{"name":"\\u6d89\\u6c34\\u9669","level":"","price":25.01888016,"disPrice":11.883968076,"noPayPrice":3.752832024,"disNoPayPrice":1.7825952114},"hh":{"name":"\\u8f66\\u8eab\\u5212\\u75d5\\u9669","level":5000,"price":1296,"disPrice":615.6,"noPayPrice":194.4,"disNoPayPrice":92.34},"zr":{"name":"\\u81ea\\u7136\\u635f\\u5931\\u9669","level":"","price":392.62450176,"disPrice":186.496638336,"noPayPrice":0,"disNoPayPrice":0}},"redamount":347.215025}', 1, 6944.30, NULL, NULL, 1451963447, NULL, 0);

-- --------------------------------------------------------

--
-- 表的结构 `yy_package`
--

CREATE TABLE IF NOT EXISTS `yy_package` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `driving_id` int(11) unsigned DEFAULT NULL COMMENT '所属行驶证id',
  `content` varchar(1500) NOT NULL DEFAULT '' COMMENT '保险套餐内容，字符串',
  `createtime` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `endTime` int(11) unsigned DEFAULT NULL COMMENT '失效时间',
  `pricesheet_id` int(11) unsigned DEFAULT NULL COMMENT '暂不用，该套餐原始报价单id',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='车辆默认保险套餐信息' AUTO_INCREMENT=8 ;

--
-- 转存表中的数据 `yy_package`
--

INSERT INTO `yy_package` (`Id`, `driving_id`, `content`, `createtime`, `endTime`, `pricesheet_id`) VALUES
(2, 1, '{"dsfzr":{"_isSelected":true,"_level":"8","_noPay":false},"clss":{"_isSelected":true,"_level":null,"_noPay":true},"qcdq":{"_isSelected":true,"_level":null,"_noPay":true},"sjzwzr":{"_isSelected":true,"_level":"4","_noPay":true},"ckzwzr":{"_isSelected":true,"_level":"4","_noPay":false},"bl":{"_isSelected":true,"_level":"1","_noPay":true},"ss":{"_isSelected":true,"_level":null,"_noPay":true},"hh":{"_isSelected":true,"_level":"2","_noPay":true},"zr":{"_isSelected":true,"_level":null,"_noPay":false},"zd":{"_isSelected":null,"_level":null,"_noPay":null}}', 1450943540, NULL, 1),
(5, 5, '{"dsfzr":{"_isSelected":true,"_level":"8","_noPay":true},"clss":{"_isSelected":true,"_level":null,"_noPay":true},"qcdq":{"_isSelected":true,"_level":null,"_noPay":true},"sjzwzr":{"_isSelected":true,"_level":"4","_noPay":false},"ckzwzr":{"_isSelected":true,"_level":"2","_noPay":true},"bl":{"_isSelected":true,"_level":"1","_noPay":true},"ss":{"_isSelected":true,"_level":null,"_noPay":false},"hh":{"_isSelected":true,"_level":"3","_noPay":true},"zr":{"_isSelected":true,"_level":null,"_noPay":false},"zd":{"_isSelected":null,"_level":null,"_noPay":null}}', 1451219726, NULL, NULL),
(4, 4, '{"dsfzr":{"_isSelected":true,"_level":"8","_noPay":true},"clss":{"_isSelected":true,"_level":null,"_noPay":true},"qcdq":{"_isSelected":true,"_level":null,"_noPay":true},"sjzwzr":{"_isSelected":null,"_level":null,"_noPay":null},"ckzwzr":{"_isSelected":null,"_level":null,"_noPay":null},"bl":{"_isSelected":null,"_level":null,"_noPay":null},"ss":{"_isSelected":null,"_level":null,"_noPay":null},"hh":{"_isSelected":null,"_level":null,"_noPay":null},"zr":null,"zd":{"_isSelected":null,"_level":null,"_noPay":null}}', 1451143010, NULL, NULL),
(6, 6, '{"dsfzr":{"_isSelected":true,"_level":"7","_noPay":true},"clss":{"_isSelected":true,"_level":null,"_noPay":true},"qcdq":{"_isSelected":true,"_level":null,"_noPay":false},"sjzwzr":{"_isSelected":true,"_level":"1","_noPay":true},"ckzwzr":{"_isSelected":true,"_level":"4","_noPay":true},"bl":{"_isSelected":null,"_level":null,"_noPay":null},"ss":{"_isSelected":null,"_level":null,"_noPay":null},"hh":{"_isSelected":null,"_level":null,"_noPay":null},"zr":null,"zd":{"_isSelected":null,"_level":null,"_noPay":null}}', 1451273260, NULL, NULL),
(7, 7, '{"dsfzr":{"_isSelected":true,"_level":"8","_noPay":true},"clss":{"_isSelected":true,"_level":null,"_noPay":true},"qcdq":{"_isSelected":true,"_level":null,"_noPay":true},"sjzwzr":{"_isSelected":true,"_level":"6","_noPay":true},"ckzwzr":{"_isSelected":true,"_level":"6","_noPay":true},"bl":{"_isSelected":true,"_level":"1","_noPay":false},"ss":{"_isSelected":true,"_level":null,"_noPay":true},"hh":{"_isSelected":true,"_level":"2","_noPay":true},"zr":{"_isSelected":true,"_level":null,"_noPay":false},"zd":{"_isSelected":null,"_level":null,"_noPay":null}}', 1451549304, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `yy_picture`
--

CREATE TABLE IF NOT EXISTS `yy_picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id自增',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `name` varchar(31) NOT NULL DEFAULT '' COMMENT '原文件名',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '图片链接',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=47 ;

--
-- 转存表中的数据 `yy_picture`
--

INSERT INTO `yy_picture` (`id`, `path`, `name`, `url`, `md5`, `sha1`, `status`, `create_time`) VALUES
(28, 'Uploads/Picture/8d4c0fc9f7c8f4e78d341ff68ace2f22.png', 'xss.png', '', 'd55e9dfbf06c13f45d9d48afb0dd1a36', '8d730286220c0c223cc7c85142afec0d238eaded', 0, 1450347269),
(27, 'Uploads/Picture/b0de7cfb888a502cab1639d151213a6d.png', 'xsf.png', '', '04d4dcd295aec8514368b6341a48b5d7', 'b642d7c115e65cec3b0032da6f4914a080cf9d1d', 0, 1450347269),
(41, 'Uploads/Picture/feefbf44d24e4d1b8b1da9a6279e6389.png', '24-201501281621413884.png', '', '32a05d3ec77316cf0dc4cf8d4e4f347a', 'ffa7632ccb0ebc0d6f0c2838dcdc8fb05b7b3498', 0, 1451219706),
(42, 'Uploads/Picture/5e14337f24d8e7dc58b69c4b329c402a.jpg', '1-27460.jpg', '', '17d0bba1e420913d9afa687b557a3e51', '53088a6116ca0c7569c8497d367596cd0d7d0f21', 0, 1451219706),
(40, 'Uploads/Picture/9447b404a69647d00feb690f2e495490.jpg', 'pic1415695952312.jpg', '', 'a60c3299d12b2ef9e262fd43710151dc', '506cc5a2be305a240ab8b931c18f59dd26089cf2', 0, 1451142603),
(39, 'Uploads/Picture/eb65cd9b92f54872166cfaa6e21a754e.jpg', 't0165fc32546f5c457b.jpg', '', '4dbf89c0be51e449f523589aed3ca5de', 'dde76a0b1901a1ad3003df414a17f76b02937963', 0, 1451142603),
(43, 'Uploads/Picture/461ab0df73af2fdec6c805d578b1372a.jpg', 'u=367402233,876693516&fm=21&gp=', '', 'b759df9487dbf08b5cc53403b651bfd4', 'b9774215d371af51c03c681debce1f5e4d356f06', 0, 1451273169),
(44, 'Uploads/Picture/a4f4e91de1dd32aa4db7075964d82a2d.jpg', 'u=835019350,3380648988&fm=21&gp', '', '5a5af718945fcedeba4c12c1ebee0a9e', '29e42738ebdd115d99fc641a0445e5f9a0d5fea4', 0, 1451273169),
(45, 'Uploads/Picture/8dcfadd7c649f03039aa63bdd2215af4.jpg', 'u=1013644147,1808250571&fm=21&g', '', '95f09d53aceecaf956da00e9d980bbeb', 'e6ff98f41e9e6d72e7dca47ce1f29ebd11618e9e', 0, 1451274216),
(46, 'Uploads/Picture/4c004e34ed56647855a40118912ffb37.jpg', 'u=1385903951,4291419676&fm=21&g', '', 'ebf6a791935c7270eac916a7386a8060', '618d1f4698ea7ebef1f9a0c264ba8308f064cf69', 0, 1451274216);

-- --------------------------------------------------------

--
-- 表的结构 `yy_policy`
--

CREATE TABLE IF NOT EXISTS `yy_policy` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='重复，暂不用，保单 保险套餐' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `yy_pricesheet`
--

CREATE TABLE IF NOT EXISTS `yy_pricesheet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `original_sheet` varchar(1279) DEFAULT NULL COMMENT '原始excel出的报价单',
  `updatetime` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `sort` varchar(255) DEFAULT NULL COMMENT '保险公司id排序',
  `order_id` int(11) unsigned DEFAULT NULL COMMENT '订单id',
  `driving_id` varchar(255) DEFAULT NULL COMMENT '报价所属行驶证',
  `deadtime` int(11) unsigned DEFAULT '0' COMMENT '失效时间',
  `status` tinyint(1) DEFAULT '0' COMMENT '报价单状态，默认0，-1过期失效',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='报价单' AUTO_INCREMENT=22 ;

--
-- 转存表中的数据 `yy_pricesheet`
--

INSERT INTO `yy_pricesheet` (`id`, `original_sheet`, `updatetime`, `sort`, `order_id`, `driving_id`, `deadtime`, `status`) VALUES
(4, '{"dsfzr":{"price":2856.5264,"level":150},"clss":{"price":7320.2675,"level":""},"qcdq":{"price":2659.32,"level":""}}', 1451197984, NULL, NULL, NULL, 0, -1),
(5, '{"dsfzr":{"price":2301.1155,"level":150},"clss":{"price":4605.75,"level":""},"qcdq":{"price":1552.128,"level":""},"sjzwzr":{"price":720,"level":20},"ckzwzr":{"price":269.1,"level":10},"bl":{"price":621,"level":""},"ss":{"price":20.025,"level":""},"hh":{"price":1863,"level":10000},"zr":{"price":384.48,"level":""}}', 1451219845, NULL, NULL, NULL, 0, 0),
(6, '{"dsfzr":{"price":1903.365,"level":100},"clss":{"price":978.075,"level":""},"qcdq":{"price":402.332,"level":""},"sjzwzr":{"price":207,"level":5},"ckzwzr":{"price":538.2,"level":20}}', 1451273463, NULL, NULL, NULL, 0, 0),
(7, '{"dsfzr":{"price":1689.2995,"noPayPrice":253.394925,"level":150},"clss":{"price":1139.8765,"noPayPrice":170.981475,"level":""},"qcdq":{"price":366.7608,"noPayPrice":73.35216,"level":""},"sjzwzr":{"price":558.6,"noPayPrice":83.79,"level":20},"ckzwzr":{"price":1436.4,"noPayPrice":215.46,"level":20},"bl":{"price":117.5055,"noPayPrice":null,"level":""},"ss":{"price":5.6993825,"noPayPrice":0.854907375,"level":""},"hh":{"price":379.05,"noPayPrice":56.8575,"level":5000},"zr":{"price":68.870592,"noPayPrice":0,"level":""}}', 1451550763, NULL, NULL, NULL, 0, 0),
(21, '{"dsfzr":{"price":2133.408,"noPayPrice":0,"level":150},"clss":{"price":5003.776032,"noPayPrice":750.5664048,"level":""},"qcdq":{"price":1194.486154752,"noPayPrice":238.8972309504,"level":""},"sjzwzr":{"price":192,"noPayPrice":28.8,"level":5},"ckzwzr":{"price":499.2,"noPayPrice":0,"level":5},"bl":{"price":656.678304,"noPayPrice":null,"level":""},"ss":{"price":25.01888016,"noPayPrice":3.752832024,"level":""},"hh":{"price":1296,"noPayPrice":194.4,"level":5000},"zr":{"price":392.62450176,"noPayPrice":0,"level":""}}', 1451963096, NULL, 2, '1', 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `yy_red`
--

CREATE TABLE IF NOT EXISTS `yy_red` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `size` smallint(2) unsigned NOT NULL DEFAULT '0' COMMENT '红包大小（总金额）',
  `cut_down` smallint(2) unsigned NOT NULL DEFAULT '0' COMMENT '已砍金额',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '红包状态',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='红包表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `yy_redshare`
--

CREATE TABLE IF NOT EXISTS `yy_redshare` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分享的客户id',
  `amount` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '分享该用户所得金额',
  `nickname` varchar(15) DEFAULT NULL COMMENT '用户昵称',
  `headimgurl` varchar(127) DEFAULT NULL COMMENT '用户头像地址',
  `phone` varchar(13) DEFAULT NULL COMMENT '手机号',
  `openid` varchar(39) DEFAULT NULL,
  `sex` tinyint(1) unsigned DEFAULT NULL,
  `city` varchar(15) DEFAULT NULL,
  `refresh_token` varchar(159) DEFAULT NULL COMMENT '刷新token',
  `createtime` int(11) unsigned DEFAULT NULL COMMENT '加入时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='红包分享准客户' AUTO_INCREMENT=6 ;

--
-- 转存表中的数据 `yy_redshare`
--

INSERT INTO `yy_redshare` (`id`, `client_id`, `amount`, `nickname`, `headimgurl`, `phone`, `openid`, `sex`, `city`, `refresh_token`, `createtime`) VALUES
(1, 2, 7, '七分十空', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM5fZz8eeZjGA9eBLcaFK8Rn0JjKhHKMich5MaOovfIFkJgxib22CtlCDlVKKR0TUYtJIqbJcuFkHv7w/0', NULL, 'oU1HqtzCxysoMyD0yAui4WF1LROY', 1, '洛阳', 'OezXcEiiBSKSxW0eoylIeHOtrAkd1obkF6-oKz7zKEl4uAzpHW_xwJHGoPd4mkp0EKozEeSy2jQh_xyL6Jboa9etFAD6OEkS9vfl2m3XO4Jn6cQ3c5-Znmr1PTQNFSKHZA5Gd9mCUt1QQzviTPJsAg', 1452153938),
(2, 2, 25, '七分十空', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM5fZz8eeZjGA9eBLcaFK8Rn0JjKhHKMich5MaOovfIFkJgxib22CtlCDlVKKR0TUYtJIqbJcuFkHv7w/0', NULL, 'oU1HqtzCxysoMyD0yAui4WF1LROY', 1, '洛阳', 'OezXcEiiBSKSxW0eoylIeHOtrAkd1obkF6-oKz7zKEl4uAzpHW_xwJHGoPd4mkp0vjhgx5x4o0DqvdASC0bH3Uc_Nj19tJMWAQXGRG0wCxdJiYvbSOu-IeHca33S-r7TrBetL20to7lHNbegqUOGkg', 1452161588),
(3, 2, 12, '七分十空', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM5fZz8eeZjGA9eBLcaFK8Rn0JjKhHKMich5MaOovfIFkJgxib22CtlCDlVKKR0TUYtJIqbJcuFkHv7w/0', NULL, 'oU1HqtzCxysoMyD0yAui4WF1LROY', 1, '洛阳', 'OezXcEiiBSKSxW0eoylIeHOtrAkd1obkF6-oKz7zKEl4uAzpHW_xwJHGoPd4mkp0ZhEbEqMZ_D0DDlY5SL_EGSwU17VDmo29wCVAvw9jbzKa3GQrqHck7UxpBQ3GnvM1yf5zQRQsMop6Im-LGhfOSA', 1452161657),
(4, 2, 36, '七分十空', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM5fZz8eeZjGA9eBLcaFK8Rn0JjKhHKMich5MaOovfIFkJgxib22CtlCDlVKKR0TUYtJIqbJcuFkHv7w/0', NULL, 'oU1HqtzCxysoMyD0yAui4WF1LROY', 1, '洛阳', 'OezXcEiiBSKSxW0eoylIeHOtrAkd1obkF6-oKz7zKEl4uAzpHW_xwJHGoPd4mkp0VOFtilJZvloZGNxNeAqmM4QDMDLG0e6P368oq__huNSBY0IDDwKqHcnProeXFfdLX16cOpWK-SfkwT0GL_sbiw', 1452161681),
(5, 2, 46, '七分十空', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM5fZz8eeZjGA9eBLcaFK8Rn0JjKhHKMich5MaOovfIFkJgxib22CtlCDlVKKR0TUYtJIqbJcuFkHv7w/0', NULL, 'oU1HqtzCxysoMyD0yAui4WF1LROY', 1, '洛阳', 'OezXcEiiBSKSxW0eoylIeHOtrAkd1obkF6-oKz7zKEl4uAzpHW_xwJHGoPd4mkp0_mmvHLfqFnflAJmJSIo0m5c7RF_HTwB5Ms6d6CsuFcQMgpGAM50rAuhfRBjwH7glPvQ-un0dnBmz5JfmqKTaPg', 1452161729);

-- --------------------------------------------------------

--
-- 表的结构 `yy_service`
--

CREATE TABLE IF NOT EXISTS `yy_service` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='客服' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `yy_serviceaction`
--

CREATE TABLE IF NOT EXISTS `yy_serviceaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客服id',
  `actiontype` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '行为类型',
  `value` varchar(31) DEFAULT NULL COMMENT '值',
  `createtime` int(11) unsigned DEFAULT NULL COMMENT '发生时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='客服行为记录' AUTO_INCREMENT=16 ;

--
-- 转存表中的数据 `yy_serviceaction`
--

INSERT INTO `yy_serviceaction` (`id`, `admin_id`, `actiontype`, `value`, `createtime`) VALUES
(1, 52, 1, NULL, 1451885265),
(2, 52, 1, NULL, 1451961463),
(3, 52, 1, NULL, 1451985908),
(4, 52, 1, NULL, 1452052221),
(5, 52, 1, NULL, 1452054622),
(6, 52, 1, NULL, 1452136823),
(7, 52, 1, NULL, 1452161317),
(8, 52, 1, NULL, 1452161523),
(9, 52, 1, NULL, 1452162151),
(10, 52, 1, NULL, 1452179621),
(11, 52, 1, NULL, 1452180677),
(12, 52, 1, NULL, 1452183334),
(13, 52, 1, NULL, 1452219765),
(14, 52, 1, NULL, 1452220555),
(15, 52, 1, NULL, 1452224029);

-- --------------------------------------------------------

--
-- 表的结构 `yy_status`
--

CREATE TABLE IF NOT EXISTS `yy_status` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(31) NOT NULL DEFAULT '' COMMENT '属性名',
  `value` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '属性值',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='全局状态表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `yy_status`
--

INSERT INTO `yy_status` (`Id`, `key`, `value`) VALUES
(1, 'current_service_id', 58);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
