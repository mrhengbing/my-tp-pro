<?php
/**
 * @Author: mrhengbing
 * @Date:   2017-02-10 22:54:02
 * @Last Modified by:   mrhengbing
 * @Last Modified time: 2017-03-09 14:27:51
 * @Email:  415671062@qq.com
 * ---------------栏目无限极分类---------
 */
class Infoclass {
    /**
     * 无限极分类1
     * 组合一维数组
     * @return [type] [description]
     */
    static public function infoclassForLevel($infoclass, $html='', $pid=0, $level=0){
        $arr = array();
        foreach ($infoclass as $v) {
            if($v['parentid'] == $pid){
                $v['level'] = $level+1;
                $v['html'] = str_repeat($html, $level);
                $arr[] = $v;
                $arr = array_merge($arr, self::infoclassForLevel($infoclass, $html, $v['id'], $level+1));
            }
        }
        return $arr;
    } 

    /**
     * 无限极分类2
     * 组合多维数组
     * @param  [type]  $cat  [description]
     * @param  [type]  $name [description]
     * @param  integer $pid  [description]
     * @return [type]        [description]
     */
    static public function infoclassForLayer($cat, $name, $pid=0){
        $arr = array();
        foreach ($cat as $v) {
            if($v['parentid'] == $pid){
                $v[$name] = self::infoclassForLayer($cat, $name, $v['id']);
                $arr[] = $v;
            }
        }
        return $arr;
    }
   
    /**
     * 传递一个子分类ID返回所有父分类
     * @param  [type] $cat [description]
     * @param  [type] $id  [description]
     * @return [type]      [description]
     */
    static public function getParents($cat, $id){
        $arr =array();
        foreach ($cat as $v) {
            if($v['id'] == $id){
                $arr[] = $v;
                $arr = array_merge(self::getParents($cat, $v['parentid']), $arr);
            }
        }
        return $arr;
    }

    /**
     * 传递父子分类ID返回所有子分类
     * @param  [type] $cat [description]
     * @param  [type] $pid [description]
     * @return [type]      [description]
     */
    static public function getChildId($cat, $pid){
        $arr =array();
        foreach ($cat as $v) {
            if($v['parentid'] == $pid){
                $arr[] = $v;
                $arr = array_merge($arr, self::getChildId($cat, $v['id']));
            }
        }
        return $arr;
    }



}