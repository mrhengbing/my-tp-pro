<?php
/**
 * @Author: mrhengbing
 * @Email:  415671062@qq.com
 * --------------栏目控制器-------------------
 */
namespace Admin\Controller;
use Think\Controller;
import('Class.Infoclass');
class InfoclassController extends CommonController {
    /**
     * 栏目分类列表
     * @return [type] [description]
     */
    public function index(){
        //查询所有栏目，并且模板赋值
        $infoclass = M('infoclass')->field('id, parentid, parentstr, infotype, classname, orderid, checkinfo')->order('orderid asc')->select();
        $html = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';     //空格
        $this->infoclass = \Infoclass::infoclassForLevel($infoclass, $html);

        //数据总条数
        $this->count = M('infoclass')->count();

        $this->display();
    }

    /**
     * 栏目添加页
     * @return [type] [description]
     */
    public function infoclassAdd(){
        //查询所有栏目
        $infoclass = M('infoclass')->order('orderid asc')->select();
        //无限级分类
        $classList = \Infoclass::infoclassForLevel($infoclass, '&nbsp;&nbsp;&nbsp;&nbsp;');

        //如果不是一级栏目，加上“|- ”
        foreach ($classList as $k => $v) {
            if($v['parentid'] != 0){
                $classList[$k]['html'] .= '|- ';
            }
        }

        //模板赋值
        $this->classList = $classList;

        //传递栏目id，并且模板赋值
        $pid = I('id', 0, 'intval');
        $this->pid = $pid;

        //查询最大的排序
        $orderid = M('infoclass')->field('orderid')->order('orderid desc')->find();
        //新建栏目默认排序为数据表中最大排序+1
        $this->orderid = $orderid['orderid']+1;

        $this->display();
    }

    /**
     * 栏目添加方法
     * @return [type] [description]
     */
    public function infoclassAddSave(){
        if(IS_POST){
            //接收所有参数
            $data = I('post.');
  
            //查询所有栏目
            $infoclass = M('infoclass')->order('orderid asc')->select();

            //查询当前类的父类的所有父类
            $parentarr = \Infoclass::getParents($infoclass, $data['parentid']);

            $parentstr = '';
            //将父类的所有父类id拼成字符串
            foreach($parentarr as $v){
                $parentstr .= $v['parentid'].',';
            }
            $parentstr .= $data['parentid'];      //补充连接当前类的父类id

            $data['parentstr'] = $parentstr;        //将父类id字符串赋给数组$data

            //获取图片路径
            if(isset($_FILES['picurl']['name']) && $_FILES['picurl']['name']!=''){
                $picurl = self::uploadify();
                $data['picurl'] = $picurl;
            }

            $result = M('infoclass')->add($data);      //将所有数据添加进数据库

            if($result){
                $this->success('添加成功！', U('index'));
            }else{
                $this->error('添加失败！');
            }
        }else{
            $this->error('页面不存在！');
        }
    }

    /**
     * 栏目修改页
     * @return [type] [description]
     */
    public function infoclassUpdate(){
        $id = I('id', '', 'intval');   //栏目id
        $infoclass = M('infoclass');      //实例化infoclass对象

        //根据id查找栏目信息
        $classInfo = $infoclass->where('id='.$id)->find();

        //查询所有栏目
        $infoclass = $infoclass->order('orderid asc')->select();
        //无限级分类
        $classList = \Infoclass::infoclassForLevel($infoclass, '&nbsp;&nbsp;&nbsp;&nbsp;');

        //如果不是一级栏目，加上“|- ”
        foreach ($classList as $k => $v) {
            if($v['parentid'] != 0){
                $classList[$k]['html'] .= '|- ';
            }
        }

        //模板赋值
        $this->classList = $classList;
        $this->classInfo = $classInfo;
        $this->display();
    }

    /**
     * 栏目修改方法
     * @return [type] [description]
     */
    public function infoclassUpdateSave(){
        if(IS_POST){
            $id         = I('id', '', 'intval');            //栏目id
            $parentid   = I('parentid', '', 'intval');        //选择父栏目的id
            $repid      = I('repid', '', 'intval');         //栏目原父id

            $infoclass = M('infoclass');   //实例化infoclass
            //查询所有栏目
            $infolist = $infoclass->order('orderid asc')->select();

             //查询当前类的所有父类
            $parentarr = \Infoclass::getParents($infolist, $id);

            $parentstr = '';
            //将栏目的所有父类id拼成字符串
            foreach($parentarr as $v){
                $parentstr .= $v['parentid'].',';
            }

            $data = array();    //定义变量$data为数组
            $data = array(
                    'id'            =>   $id,
                    'parentid'      =>   $parentid,
                    'parentstr'     =>   $parentstr,
                    'infotype'      =>   I('infotype', '', 'intval'),
                    'classname'     =>   I('classname'),
                    'content'       =>   I('content'),
                    'seotitle'      =>   I('seotitle'),
                    'keywords'      =>   I('keywords'),
                    'description'   =>   I('description'),
                    'orderid'       =>   I('orderid', '', 'intval'),
                    'checkinfo'     =>   I('checkinfo')
                );

            //不允许更新parentid为自己的
            if($parentid != $id){
                //更新所有关联parentstr
                /*if($parentid != $repid){
                    $childtbname = array('infolist','infoimg');     //和parentstr关联的表
                    //更新本类parentstr
                    foreach($childtbname as $k=>$v)
                    {
                        M($v)->where('classid = '.$id)->save(array('parentid'=>$parentid, 'parentstr'=>$parentstr));
                    }
                }*/
                //获取图片路径
                if(isset($_FILES['picurl']['name']) && $_FILES['picurl']['name']!=''){
                    $picurl = self::uploadify();
                    $data['picurl'] = $picurl;
                }
                //更新数据
                $infoclass->save($data);
                $this->success('修改成功！', U('index'));
                
            }else{
                $this->error('不允许选择本身作为所属父类！');
            }
        }else{
            $this->error('页面不存在！');
        }
    }

    /**
     * 删除栏目
     * @return [type] [description]
     */
    public function delClass(){
        $id = I('id', '', 'intval');    //栏目id

        //删除栏目及其子栏目
        $result = M('infoclass')->where('id = '.$id.' or parentstr like "%,'.$id.',%"')->delete();

        if($result){
            $this->success('删除成功！', U('index'));
        }else{
            $this->error('删除失败！');
        }
    }

    /**
     * 多选删除栏目
     * @return [type] [description]
     */
    public function delAllClass(){
        $checkid = I('checkid');    //栏目id

        //删除栏目
        foreach($checkid as $k=>$v)
        {     
            //删除栏目
            $result = M('infoclass')->where('id = '.$v.' or parentstr like "%,'.$v.',%"')->delete();
        }
        if($result){
            $this->success('删除成功！', U('index'));
        }else{
            $this->error('删除失败！');
        }
    }

    /**
     * 修改栏目状态
     * @return [type] [description]
     */
    public function check(){
        $id = I('id', '', 'intval');    //栏目id

        $infoclass = M('infoclass');  //实例化infoclass

        //查询栏目状态
        $classInfo = $infoclass->field('checkinfo')->where('id = '.$id)->find();
        if($classInfo['checkinfo'] == 'true'){
            //更新栏目状态
            $infoclass->where('id = '.$id)->save(array('checkinfo'=>'false'));
        }else{
            //更新栏目状态
            $infoclass->where('id = '.$id)->save(array('checkinfo'=>'true'));
        }

       // $this->success('更新成功！', U('index'));
      
    }  

    /**
     * 更新排序
     * @return [type] [description] 
     */
    public function updateOrderid(){
        $id = I('id', '', 'intval');       //所有栏目id
        $orderid = I('orderid', '', 'intval');  //栏目排序

        //更改排序
        $result = M('infoclass')->where('id = '.$id)->save(array('orderid'=>$orderid));
        
        if($result){
            $data = M('infoclass')->field('orderid')->where('id = '.$id)->find();
            return $data['orderid'];
        }

    }


}