<?php  

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('COOKIE_PASSWARD', 'zsign2015hd');
define('REGISTER_COOKIE', 'sign_2015');
define('STEP_COOKIE', 'step');
define('UID_COOKIE', 'uid');
define('TYPE_COOKIE', 'type');
define('NOMAL_USER', 1); //普通岛民用户类型
define('VIP_USER', 2); //免费岛民用户类型
define('NON_USER', 3); //非岛民用户类型
define('USER_COST', '3000'); //岛民费用
define('ACT_ID', '10197'); //活动ID
define('PAY_URL', 'http://www.baidu.com'); //活动ID

$config['gapi'] = array(
        'url' => 'http://act.zhisland.com/gapi',
        //'url' => 'http://www.zhisland.com/gapi',
        'app_key' => 'ii3aAkeycS',
        'app_secret' => '#sjuejAik8)(#1',
);

//推广级别
$config['level'] = array();

//报名步骤
$config['sign_step'] = array(
	'0' => 'login',
	'1' => 'basic',
	'2' => 'route',
	'3' => 'invoice',
	'4' => 'confirm',
);

//行程顺序 值为行程id
$config['route'] = array(
    1 => '云南白药',
    2 => '沃森生物',
    3 => '云南翡翠探秘',
    4 => '运动嘉年华',
    5 => '三对三篮球',
    6 => '五人制足球',
    7 => '乒乓球',
    8 => '游泳',
    9 => '棋牌-惯蛋',
    10=> '棋牌-桥牌',
    11=> '棋牌-中国象棋',
    12=> '行走滇池',
    13=> '高尔夫球赛（12月30日全天，费用AA制）'
);
