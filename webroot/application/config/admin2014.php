<?php  
//61报名后台

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//define('IMG_PATH', '/www/web/root/jiayan_register/webroot/uploads/');
define('IMG_PATH', '/home/lanjinwei/code/CIS/_app/zhshare/branches/webroot/uploads/');
define('IMG_DIR', 'imgs_gbk');
define('SMS_MOBILE_PATTERN', '/^1[3458]\d{9}$/');
//define('IMG_PREFIX', 'http://capi.zhisland.com');
define('IMG_PREFIX', 'http://denglianwen.deving.zhisland.com');

define('MEMCACHE_CFG_HOST', '127.0.0.1');
define('MEMCACHE_CFG_PORT', '11211');
define('MEMCACHE_CFG_WEIGHT', '1');
define('MEMCACHE_INFOS', MEMCACHE_CFG_HOST . ':' . MEMCACHE_CFG_PORT . ':' . MEMCACHE_CFG_WEIGHT . ';');
define('DELAY_TIME', 10*60);  //单位秒

//图片相关配置
$config['upload'] = array(
//为了解决下载中文文件名乱码
    //'imgs_utf8_path'     => '/www/web/root/jiayan_register/webroot/uploads/imgs_utf8/',
    'imgs_utf8_path'     => '/home/lanjinwei/code/CIS/_app/zhshare/branches/webroot/uploads/imgs_utf8/',
    //'imgs_gbk_path'     =>'/www/web/root/jiayan_register/webroot/uploads/imgs_gbk/',
    'imgs_gbk_path'     =>'/home/lanjinwei/code/CIS/_app/zhshare/branches/webroot/uploads/imgs_gbk/',
    'html_path'     => 'uploads/',
    'img_dir'      => 'uploads',
);

$config['role'] = array(
    '1' => array('18701008325', '13911374767', '13911285280', '13911374767'), //管理员
    '2' => array('18811367471'),//确认付款账号
    '3' => array('18626882625'),//查询邀请码账号
);

//后台相关配置
$config['admin'] = array(
    //'base_url' => 'http://admin_register.zhisland.com/admin2014/page/',
    //'base_url' => 'http://192.168.2.69:8005/admin2014/page/$registerStatus/$is_pay',
    'per_page' => '20',
    'uri_segment' => '5',
    'num_links' => '3',
    'next_link' => '下一页',
    'prev_link' => '上一页',
    'last_link' => '末页',
    'first_link' => '首页',
    'time_file' => '/tmp/', //为了增量导出，记录的时间
);

$config['excelOther'] = array(
    'id'      => '流水id', 
	'usertype' => '嘉宾类别', 
	'uname'    => '姓名', 
	'mobile'   => '手机',
	'submit_time' => '注册时间',
	'time' => '导出时间',
);

//导出excel
$config['excel'] = array(
    'id'      => '流水id', 
    'zhid'      => '网站id', 
	'usertype' => '嘉宾类别', 
//	'service' => '服务经理', 
	'uname'    => '姓名', 
//	'gender'   => '性别', 
	'company'  => '公司名称',
	'title'    => '职务',
	'mobile'   => '手机',
//	'location'   => '区域',
//	'fax'      => '传真',
//	'telephone'=> '固话',
	'email'    => '电子邮件',
	'sum_all'      => '活动总金额',
	'is_pay'      => '是否付款',
	'pay_type_str' => '付款类型',
	'pay_no'	=> '付款流水号',
	'is_signed'      => '是否报名',
	'province'      => '地域',
	'industry'      => '行业',
	'service_name'      => '服务经理',
	//'usga'      => '营业额',
	'turnover'      => '营业额',
	//'id_card'      => '旧数据家属',
	'relation_num'      => '家属数',
	//'child_num'      => '家属数',
	'achild_name'      => '家属1姓名',
	'achild_age'      => '家属1年龄',
	'achild_gender'      => '家属1性别',
	'achild_class'      => '家属1关系',
	'bchild_name'      => '家属2姓名',
	'bchild_age'      => '家属2年龄',
	'bchild_gender'      => '家属2性别',
	'bchild_class'      => '家属2关系',
	//'cchild_name'      => '孩子3姓名',
	//'cchild_age'      => '孩子3年龄',
	//'cchild_gender'      => '孩子3性别',
	//'cchild_class'      => '孩子3年级',
    /*
    'contact_name'   => '联系人姓名',
	'contact_gender' => '联系人性别',
	'contact_title' => '联系人职务',
	'contact_mobile' => '联系人手机',
	'contact_email'  => '联系人电子邮件',
	'contact_address' => '联系人地址',
	'contact_code'   => '联系人邮编',
	'needpickup'     => '是否需要接机',
	'pu_date'        => '到港日期', 
	'pu_time'        => '抵达时间', 
	'pu_flight'      => '接机航班号', 
	'needlodging'    => '是否需要送机', 
	'po_date'        => '离港日期',
	'po_time'        => '离港时间',
	'poo_flight'      => '送机航班号',
    */
    /*
	'aname'      => '同行人员1姓名',
	'amobile'      => '同行人员1手机号',
	'aid_card'    => '同行人员1身份证',
	'aneedpickup'      => '同行人员1接机情况',
	'apu_date'      => '同行人员1到港日期',
	'apu_time'      => '同行人员1抵达时间',
	'apu_flight'      => '同行人员1接机航班号',
	'aneedlodging'      => '同行人员1送机情况',
	'apo_date'      => '同行人员1离港日期',
	'apo_time'      => '同行人员1起飞时间',
	'bname'      => '同行人员2姓名',
	'bmobile'      => '同行人员2手机号',
	'bid_card'    => '同行人员2身份证',
	'bneedpickup'      => '同行人员2接机情况',
	'bpu_date'      => '同行人员2到港日期',
	'bpu_time'      => '同行人员2抵达时间',
	'bpu_flight'      => '同行人员2接机航班号',
	'bneedlodging'      => '同行人员2送机情况',
	'bpo_date'      => '同行人员2离港日期',
	'bpo_time'      => '同行人员2起飞时间',
	'cname'      => '同行人员3姓名',
	'cmobile'      => '同行人员3手机号',
	'cid_card'    => '同行人员3身份证',
	'cneedpickup'      => '同行人员3接机情况',
	'cpu_date'      => '同行人员3到港日期',
	'cpu_time'      => '同行人员3抵达时间',
	'cpu_flight'      => '同行人员3接机航班号',
	'cneedlodging'      => '同行人员3送机情况',
	'cpo_date'      => '同行人员3离港日期',
	'cpo_time'      => '同行人员3起飞时间',
	'dname'      => '同行人员4姓名',
	'dmobile'      => '同行人员4手机号',
	'did_card'    => '同行人员4身份证',
	'dneedpickup'      => '同行人员4接机情况',
	'dpu_date'      => '同行人员4到港日期',
	'dpu_time'      => '同行人员4抵达时间',
	'dpu_flight'      => '同行人员4接机航班号',
	'dneedlodging'      => '同行人员4送机情况',
	'dpo_date'      => '同行人员4离港日期',
	'dpo_time'      => '同行人员4起飞时间',
	'ename'      => '同行人员5姓名',
	'emobile'      => '同行人员5手机号',
	'eid_card'    => '同行人员5身份证',
	'eneedpickup'      => '同行人员5接机情况',
	'epu_date'      => '同行人员5到港日期',
	'epu_time'      => '同行人员5抵达时间',
	'epu_flight'      => '同行人员5接机航班号',
	'eneedlodging'      => '同行人员5送机情况',
	'epo_date'      => '同行人员5离港日期',
	'epo_time'      => '同行人员5起飞时间',
	'fname'      => '同行人员6姓名',
	'fmobile'      => '同行人员6手机号',
	'fid_card'    => '同行人员6身份证',
	'fneedpickup'      => '同行人员6接机情况',
	'fpu_date'      => '同行人员6到港日期',
	'fpu_time'      => '同行人员6抵达时间',
	'fpu_flight'      => '同行人员6接机航班号',
	'fneedlodging'      => '同行人员6送机情况',
	'fpo_date'      => '同行人员6离港日期',
	'fpo_time'      => '同行人员6起飞时间',
	'needaccom'      => '是否需要住宿',
	'ritzcarlton'      => '丽思卡尔顿',
	'room_type'      => '丽思卡尔顿房间类型',
	'num'      => '丽思卡尔顿预订数',
	'checkin_time'   => '丽思卡尔顿入住日期', 
	'checkout_time'  => '丽思卡尔顿退房日期',
	'days'  => '丽思卡尔顿入住天数',
	'2013-12-26'  => '丽思卡尔顿12月26日',
	'2013-12-27'  => '丽思卡尔顿12月27日',
	'2013-12-28'  => '丽思卡尔顿12月28日',
	'2013-12-29'  => '丽思卡尔顿12月29日',
	'2013-12-30'  => '丽思卡尔顿12月30日',
	'2013-12-31'  => '丽思卡尔顿12月31日',
	'2014-01-01'  => '丽思卡尔顿1月1日',
	'2014-01-02'  => '丽思卡尔顿1月2日',
	'2014-01-03'  => '丽思卡尔顿1月3日',
	'hilton'      => '希尔顿',
	'room_type1'      => '希尔顿房间类型',
	'num1'      => '希尔顿预订数',
	'checkin_time1'   => '希尔顿入住日期', 
	'checkout_time1'  => '希尔顿退房日期',
	'days1'  => '希尔顿入住天数',
	'2013-12-26_hilton'  => '希尔顿12月26日',
	'2013-12-27_hilton'  => '希尔顿12月27日',
	'2013-12-28_hilton'  => '希尔顿12月28日',
	'2013-12-29_hilton'  => '希尔顿12月29日',
	'2013-12-30_hilton'  => '希尔顿12月30日',
	'2013-12-31_hilton'  => '希尔顿12月31日',
	'2014-01-01_hilton'  => '希尔顿1月1日',
	'2014-01-02_hilton'  => '希尔顿1月2日',
	'2014-01-03_hilton'  => '希尔顿1月3日',
    */
	'invoice_type'   => '发票类型',
	'invoice_company'  => '开票公司名称',
	'invoice_number'  => '纳税人识别号',
	'invoice_address'  => '税务登记地址',
	'invoice_phone'  => '税务登记电话',
	'invoice_bank'  => '开户行',
	'invoice_account'  => '账号',
	'invoice_title'  => '发票抬头',
    'route_1' => '云南白药',
    'route_2' => '沃森生物',
    'route_3' => '云南翡翠探秘',
    'route_4' => '运动嘉年华',
    'route_5' => '三对三篮球',
    'route_6' => '五人制足球',
    'route_7' => '乒乓球',
    'route_8' => '游泳',
    'route_9' => '棋牌-惯蛋',
    'route_10'=> '棋牌-桥牌',
    'route_11'=> '棋牌-中国象棋',
    'route_12'=> '行走滇池',
    'route_13'=> '高尔夫球赛（12月30日全天，费用AA制）',
    /*
	'mon_0'  => '岛亲企业展示',
	'mon_4'  => '13:30-18:00 高尔夫活动（费用AA制）',
	'mon_5'  => '14:00-17:00 正和岛私董会年会',
	'mon_6'  => '14:00-17:00 广誉远养生大讲堂',
	'mon_3'  => '15:00-18:00 部落酋长\岛邻机构秘书长新品体验会',
	'mon_1'  => '18:30-21:00 欢迎晚宴',
	'mon_2'  => '全天闲暇时间-亲子活动',
	'tue_0'  => '09:30-12:35 眺望2014报告会',
	'tue_5'  => '13:30-15:30 强基因年度总结会',
	'tue_2'  => '14:00-17:00  新岛邻活动营-岛亲风采 部落故事',
	'tue_1'  => '15:00-18:00  区域机构主席闭门会议',
	'tue_3'  => '20:30-24:00  VOLVO CAR~新年家宴',
	'wed_0'  => '自助游',
	'wed_1'  => '13:30-18:00  正和岛高尔夫总决赛',
	'usga'  => '高尔夫差点',
    */
	//'is_exception' => '住宿信息异常',
	'register_time'   => '报名时间',
	'submit_time'   => '注册时间',
	'time' => '导出时间',
);

//行程id
$config['route_id'] = array(
    'mon' => array(
	    '0' => '岛亲企业展示',
	    '1' => '18:30-21:00 欢迎晚宴',
	    '2' => '全天闲暇时间-亲子活动',
	    '3' => '15:00-18:00 部落酋长\岛邻机构秘书长新品体验会',
	    '4' => '13:30-18:00 高尔夫活动（费用AA制）',
	    '5' => '14:00-17:00 正和岛私董会年会',
	    '6' => '14:00-17:00 广誉远养生大讲堂',
	),
	'tue' => array(
	    '0' => '09:30-12:35 眺望2014报告会',
	    '1' => '15:00-18:00  区域机构主席闭门会议',
	    '2' => '14:00-17:00  新岛邻活动营-岛亲风采 部落故事',
	    '3' => '20:30-24:00  VOLVO CAR~新年家宴',
	    '5' => '13:30-15:30 强基因年度总结会',
	    //'6' => '14:00-16:00 人力资本部落主题活动-大数据下的人力资源管理',
	),
	'wed' => array(
	    '0' => '自助游',
        '1' => '13:30-18:00  正和岛高尔夫总决赛',
        //'2' => '10:00-11:30  海南中西部招商引资对接会',
    ),
);

//行程顺序 值为行程id
$config['route'] = array(
    'mon' => array('0', '4', '5', '6', '3', '1', '2'),
    'tue' => array('0', '5', '2', '1', '3'),
    'wed' => array('0', '1'),
);

$config['radio'] = array(
    'gender' => array('1'=>'男', '2'=> '女', '-1'=>'未选择'),
    'usertype' => array('3'=>'非岛民', '0'=>'媒体记者', '1'=> '普通岛民', '2'=>'免费岛邻', '-1'=>'未知'),
    'invoice' => array('0'=>'需要', '1'=> '不需要'),
    'needpickup' => array('0'=>'是', '1'=> '否', '2'=>'暂无航班信息'),
    'needlodging' => array('0'=>'是', '1'=> '否', '2'=>'暂无航班信息'),
    'aneedpickup' => array('0'=>'其他航班', '1'=> '和我同行', '2'=>'不需要接机'),
    'aneedlodging' => array('0'=>'其他航班', '1'=> '和我同行', '2'=>'不需要送机'),
    'needaccom' => array('0'=>'否', '1'=> '金茂三亚丽思卡尔顿酒店', '2'=>'金茂三亚希尔顿大酒店', '3'=>'选2间酒店'),
    'invoice_type' => array('0'=>'不要发票', '1'=> '增值税发票', '2'=>'增值税普通发票'),
    'invoice_title' => array('1'=> '技术服务费', '2'=>'服务费'),
);
