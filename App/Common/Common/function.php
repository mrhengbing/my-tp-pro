<?php
/**
 * @Author: mrhengbing
 * @Email:  415671062@qq.com
 * @Date:   2016-04-21 23:34:19
 * @Last Modified by:   mrhengbing
 * @Last Modified time: 2016-04-27 23:14:59
 */
/**
 * 打印
 * @param  [type] $arr [description]
 * @return [type]      [description]
 */
function p($arr){
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}


/*
 * 截取指定长度的字符串
 *utf-8专用 汉字和大写字母长度算1，其它字符长度算0.5
 * @param  string  $str  原字符串
 * @param  int     $len  截取长度
 * @param  string  $etc  省略字符...
 * @return string        截取后的字符串
 */
function ReStrLen($str, $len=10, $etc='...')
{
    $restr = '';
    $i = 0;
    $n = 0.0;

    //字符串的字节数
    $strlen = strlen($str);
    while(($n < $len) and ($i < $strlen))
    {
       $temp_str = substr($str, $i, 1);

       //得到字符串中第$i位字符的ASCII码
       $ascnum = ord($temp_str);

       //如果ASCII位高与252
       if($ascnum >= 252)
       {
            //根据UTF-8编码规范，将6个连续的字符计为单个字符
            $restr = $restr.substr($str, $i, 6);
            //实际Byte计为6
            $i = $i + 6;
            //字串长度计1
            $n++;
       }
       else if($ascnum >= 248)
       {
            $restr = $restr.substr($str, $i, 5);
            $i = $i + 5;
            $n++;
       }
       else if($ascnum >= 240)
       {
            $restr = $restr.substr($str, $i, 4);
            $i = $i + 4;
            $n++;
       }
       else if($ascnum >= 224)
       {
            $restr = $restr.substr($str, $i, 3);
            $i = $i + 3 ;
            $n++;
       }
       else if ($ascnum >= 192)
       {
            $restr = $restr.substr($str, $i, 2);
            $i = $i + 2;
            $n++;
       }

       //如果是大写字母 I除外
       else if($ascnum>=65 and $ascnum<=90 and $ascnum!=73)
       {
            $restr = $restr.substr($str, $i, 1);
            //实际的Byte数仍计1个
            $i = $i + 1;
            //但考虑整体美观，大写字母计成一个高位字符
            $n++;
       }

       //%,&,@,m,w 字符按1个字符宽
       else if(!(array_search($ascnum, array(37, 38, 64, 109 ,119)) === FALSE))
       {
            $restr = $restr.substr($str, $i, 1);
            //实际的Byte数仍计1个
            $i = $i + 1;
            //但考虑整体美观，这些字条计成一个高位字符
            $n++;
       }

       //其他情况下，包括小写字母和半角标点符号
       else
       {
            $restr = $restr.substr($str, $i, 1);
            //实际的Byte数计1个
            $i = $i + 1;
            //其余的小写字母和半角标点等与半个高位字符宽
            $n = $n + 0.5;
       }
    }

    //超过长度时在尾处加上省略号
    if($i < $strlen)
    {
       $restr = $restr.$etc;
    }

    return $restr;
}
