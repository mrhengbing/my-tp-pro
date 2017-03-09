<?php
/**
 * @Author: mrhengbing
 * @Create time:   2017-02-09 22:22:25
 * @Last Modified by:   mrhengbing
 * @Last Modified time: 2017-03-09 14:23:43
 * @Email:  415671062@qq.com
 * @---------后台公共函数------------
 */
/*
 * 获取parentstr的第二位
 *
 * @access public
 * @param  $str    string  要拆分的整型序列如1,2,3
 * @param  $i     int     为空返回str数组的第二位(第一位为0)
 * @return $topid  int     str的第一位
*/
function getTopID($str, $i=1){
    if($str == '0,'){
        $topid = 0;
    }else{
        $ids = explode(',', $str);
        $topid = isset($ids[$i]) ? $ids[$i] : '';
    }

    return $topid;
}

/**
 * 基础分页的相同代码封装，使前台的代码更少
 * @param $count 要分页的总记录数
 * @param int $pagesize 每页查询条数
 * @return \Think\Page
 */
function getPage($count, $pagesize = 10) {
    $p = new Think\Page($count, $pagesize);
    $p->setConfig('header', '&nbsp;&nbsp;&nbsp;共<span>%TOTAL_ROW%</span>条记录&nbsp;第<span>%NOW_PAGE%</span>页/共<b>%TOTAL_PAGE%</span>页');
    $p->setConfig('prev', '上一页');
    $p->setConfig('next', '下一页');
    $p->setConfig('last', '末页');
    $p->setConfig('first', '首页');
    $p->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
    $p->lastSuffix = false;//最后一页不显示为总页数
    return $p;
}