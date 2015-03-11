<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
//判断邮件的格式是否正确
function checkEmail($string)
{
    $return = preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$string);
    if($return>0){
        return true;
    } else {
        return false;
    }
}

//判断电话号码的格式是否正确
function checkTel($string)
{
    $string = trim($string);
    $return = preg_match("/^1[3458]\d{9}$/", $string);
    if ($return > 0) {
        return true;
    }else{
        return false;
    }
}

//判断网址是否正确
function checkWeb($string){
$return=preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/",$string);
if($return>0){
   return true;
}else{
   return false;
}
}

//判断是否为空
function isNull($string)
{
    if (!isset($string) || $string == null) {
        return true;
    }else{
       return false;
    }
}

//判断姓名长度是否4-30
function checkNameLen($string){
if(mb_strlen($string)>=4&&mb_strlen($string)<=30){
   return true;
}else{
   return false;
}
}

//判断姓名是否为汉字
function checkName($string){
//$return=preg_match("/^[\x7f-\xff]+$/",$string);
$return=preg_match("/^[\x7f-\xff]+$/",$string);
if($return>0){
   return true;
}else{
   return false;
}
}
