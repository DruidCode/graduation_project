<?php
	###加载ms系统
	require_once(dirname(__FILE__) . '/./ms.lib.php');
	/*
            全局独立功能函数，判断某个用户的版本号码，用于灰度发布
	    $dir = 当前代码版本
            return 用户实际版本
        */
	require_once(dirname(__FILE__) . '/../config/__user_version.cfg.php');
	function __user_app_version($dir) {
		$pos = strpos($dir, 'webroot');
		$ver = substr($dir, $pos+strlen('webroot'), strlen($dir)-$pos);
		if( isset($GLOBALS['_user_version_'][__user_id()]) ) {
			$my_ver = ($GLOBALS['_user_version_'][__user_id()]);
			if( $ver != $my_ver) {
				return $my_ver;
			} 
		} else {
			return false;
		}
		return false;
	}

	function __user_id() {
		return 0;
	} 
