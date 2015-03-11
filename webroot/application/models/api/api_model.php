<?php
if(!defined('BASEPATH'))  exit('No direct script access allowed');

class Api_model extends CI_model {

    function __construct()
    {
        parent :: __construct();
    }
	
/*liufang edit
 * 简单的异或加密
 * @param $str 需要加密或解密的字符串 $key 密钥
 **/
public function str_encrypt($str, $key)
{
    $txt = '';
    $keylen = strlen($key);
    $strlen = strlen($str);
    for ($i=0; $i<$strlen; $i++) {
        $k = $i%$keylen;
        $txt .= $str[$i] ^ $key[$k];
    }   
    return $txt;
}

    //判断邮件的格式是否正确
    public function checkEmail($string)
    {
        $return = preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$string);
        if($return>0){
            return true;
        } else {
            return false;
        }
    }

    //判断电话号码的格式是否正确
    public function checkTel($string)
    {
        $string = trim($string);
        $return = preg_match("/^1[34578]\d{9}$/", $string);
        if ($return > 0) {
            return true;
        }else{
            return false;
        }
    }

    //判断验证码格式
    public function checkVcode($code)
    {
        $code = trim($code);
        if (is_numeric($code) && strlen($code) == 6) {
            return true;
        } else {
            return false;
        }
    }

    //判断网址是否正确
    public function checkWeb($string)
    {
        $return=preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/",$string);
    if ($return>0) {
        return true;
    }else{
        return false;
    }
    }

//判断是否为空
public function isNull($string)
{
    $string = trim($string);
    if (empty( $string ) ) {
        return false;
    }else{
       return true;
    }
}

//判断姓名长度是否4-30
public function checkNameLen($string){
if(mb_strlen($string)>=4&&mb_strlen($string)<=30){
   return true;
}else{
   return false;
}
}

//判断姓名是否为汉字
public function checkName($string){
//$return=preg_match("/^[\x7f-\xff]+$/",$string);
$return=preg_match("/^[\x7f-\xff]+$/",$string);
if($return>0){
   return true;
}else{
   return false;
}
}
	//前端输出
    public function xNetOut($code = 0, $msg = "", $data = "", $content_type='javascript')
    {
        if ($content_type == 'javascript') {
            header("Content-Type: text/javascript; charset=utf-8");
        }
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        // code field for json output
        $out = array('code' => $code);
        if ($code < 0) {
            $out['msg'] = "xNetOut Error: the 'code' parameter should not less than 0.";
            echo json_encode($out);
            return;
        }

        // msg field for json output
        if ($msg !== "") {
            $out['msg'] = $msg;
        } else if ($code > 0 && $msg === "") {
            $out['msg'] = "xNetOut Error: the 'msg' parameter should define if the 'code' parameter great than 0." ;
            echo json_encode($out);
            return;
        }

        // data field for json output
        if ($data !== "") {
            $out['data'] = $data;
        }

        // output the json structure as string.
        echo json_encode($out);
	}

    //进制转换 兼容32位系统
    public function str_baseconvert($str, $frombase=10, $tobase=36) 
    {
        $str = trim($str);
        if (intval($frombase) != 10) {
            $len = strlen($str);
            $q = 0;
            for ($i=0; $i<$len; $i++) {
                $r = base_convert($str[$i], $frombase, 10);
                $q = bcadd(bcmul($q, $frombase), $r);
            }
        }
        else $q = $str;

        if (intval($tobase) != 10) {
            $s = '';
            while (bccomp($q, '0', 0) > 0) {
                $r = intval(bcmod($q, $tobase));
                $s = base_convert($r, 10, $tobase) . $s;
                $q = bcdiv($q, $tobase, 0);
            }
        }
        else $s = $q;

        return $s;
    }

function cookie($name,$value='',$option=null)
{
    // 默认设置
    $config = array(
        'prefix' => 'zhisland_', // cookie 名称前缀
        'expire' => 30*24*3600, // cookie 保存时间
        'path'   => '/',   // cookie 保存路径
        'domain' => '', // cookie 有效域名
    );

    // 参数设置(会覆盖黙认设置)
    if (!empty($option)) {
        if (is_numeric($option)) {
            $option = array('expire'=>$option);
        }else if( is_string($option) ) {
            parse_str($option,$option);
    	}
    	$config	=	array_merge($config,array_change_key_case($option));
    }

    // 清除指定前缀的所有cookie
    if (is_null($name)) {
       if (empty($_COOKIE)) return;
       // 要删除的cookie前缀，不指定则删除config设置的指定前缀
       $prefix = empty($value)? $config['prefix'] : $value;
       if (!empty($prefix))// 如果前缀为空字符串将不作处理直接返回
       {
           foreach($_COOKIE as $key=>$val) {
               if (0 === stripos($key,$prefix)){
                    setcookie($_COOKIE[$key],'',time()-365*24*3600,$config['path']);
                    unset($_COOKIE[$key]);
               }
           }
       }
       return;
    }
    $name = $config['prefix'].$name;

    if (''===$value){
        //return isset($_COOKIE[$name]) ? unserialize($_COOKIE[$name]) : null;// 获取指定Cookie
        return isset($_COOKIE[$name]) ? ($_COOKIE[$name]) : null;// 获取指定Cookie
    }else {
        if (is_null($value)) {
            setcookie($name,'',time()-365*24*3600,$config['path']);
            unset($_COOKIE[$name]);// 删除指定cookie
        }else {
            // 设置cookie
            $expire = !empty($config['expire'])? time()+ intval($config['expire']):0;
            //setcookie($name,serialize($value),$expire,$config['path'],$config['domain']);
            setcookie($name,($value),$expire,$config['path']);
            //$_COOKIE[$name] = ($value);
        }
    }
}

/**
 * judgeDoubleType 
 * 判断字符的utf8字节数 
 * @param mixed $char 
 * @access public
 * @return int
 */
function judgeDoubleType($char){
    $asc    = ord($char);
    $i      = 0;
    if($asc > 127){
        if($asc >= 192 && $asc <= 223) $i++; //双字节
        elseif($asc >= 224 && $asc <= 239) $i += 2; //三字节 
        elseif($asc >= 240 && $asc <= 247) $i += 3; //四字节
    }
    return $i;
}

/**
  * mbStr 
  * 按照指定长度截取字符串 
  * @param mixed $str 
  * @param int $length 
  * @access public
  * @return void
  */
 function mbStr($str, $length = 140){
     $limit  = 0;
     $strLen = 0;
     for($i = 0; $i < strlen($str); $i++){
         $res = $this->judgeDoubleType($str[$i]);
         if($res == 0){
             $strLen += 0.5;
         }else{
             $strLen += 1;
             $i += $res;
         }
         if(ceil($strLen) > $length){
             break;
         }
         $limit++;
     }
     $slice = mb_substr($str, 0, $limit, 'UTF8');
     return $slice;
 }


}
