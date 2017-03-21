<?php
/**
 * @Author: mrhengbing
 * @Create time:   2017-02-15 11:13:50
 * @Last Modified by:   mrhengbing
 * @Last Modified time: 2017-03-21 16:14:02
 * @Email:  415671062@qq.com
 * @----------文章模块控制器-------------
 */
namespace Admin\Controller;
use Think\Controller;
class InfolistController extends CommonController {
    /**
     * 初始化
     */
    function _initialize(){
       // parent::isModelAuth('infolist');    //权限验证
        parent::setLog();    //更新操作日志
    }

    /**
     * 文章列表页
     * @return [type] [description]
     */
    public function index(){
        $count  = M('infolist')->where('delstate=0')->count();// 查询满足要求的总记录数
        $Page   = getPage($count, 20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show   = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = M('infolist')->field('id, classid, parentid, title, flag, posttime, hits, orderid, checkinfo, delstate')->where('delstate=0')->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        foreach ($list as $k => $v) {
            //查找栏目名称
            $infoclass = M('infoclass')->field('classname')->where('id='.$v['classid'])->find();
            $list[$k]['classname'] =  $infoclass['classname'];

            $list[$k]['title'] .= '<span class="titflag">';

            //属性标记
            $list[$k]['flag'] = explode(',', $v['flag']);
            $flagnum = count($list[$k]['flag']);
            for($i=0; $i<$flagnum; $i++){
                $flag = M('infoflag')->field('flagname')->where('flag="'.$list[$k]['flag'][$i].'"')->find();
                if(isset($flag['flagname'])){
                    $list[$k]['title'] .= '['.$flag['flagname'].'] ';
                }
            }
            ;
            $list[$k]['title'] .= '</span>';

             //将时间戳转化为时间
            $list[$k]['posttime'] = date('Y-m-d H:i:s', $v['posttime']);
        }
    
        $this->list = $list;  //$this->assign('list',$list);// 赋值数据集

        $this->page = $show;  //$this->assign('page',$show);// 赋值分页输出

         $this->display();
    }

    /**
     * 文章添加页
     * @return [type] [description]
     */
    public function infolistAdd(){
        //查询所有栏目
        $infoclass = M('infoclass')->order('orderid asc')->select();
        //无限级分类
        import('Class.Infoclass');
        $classList = \Infoclass::infoclassForLevel($infoclass, '&nbsp;&nbsp;&nbsp;&nbsp;');

        //如果不是一级栏目，加上“|- ”
        foreach ($classList as $k => $v) {
            if($v['parentid'] != 0){
                $classList[$k]['html'] .= '|- ';
            }
        }
        //模板赋值
        $this->classList = $classList;

        //属性标记
        $flag = M('infoflag')->select();
        $this->flag = $flag;

        //查询最大的排序
        $orderid = M('infolist')->field('orderid')->order('orderid desc')->find();
        //新添加文章默认排序为数据表中最大排序+1
        $this->orderid = $orderid['orderid']+1;

        //设置初始点击次数
        $hits = rand(100, 200);
        $this->hits = $hits;

        $this->display();
    }

    /**
     * 文章添加方法
     * @return [type] [description]
     */
    public function infolistAddSave(){
        if(IS_POST){
            //接收所有参数
            $data = I('post.');

            $classid = I('classid', '', 'intval');      //所属栏目id

            //查询所属栏目父id
            $infoclass = M('infoclass')->field('parentid')->where('id='.$classid)->find();

            //将所属栏目的父id插入$data中
            $data['parentid'] = $infoclass['parentid'];

            //文章属性
            $flag = I('flag');  
            if(is_array($flag))
            {
                $flag = implode(',', $flag);
            }

            $data['flag'] = $flag;

            //获取图片路径
            /*if(isset($_FILES['picurl']['name']) && $_FILES['picurl']['name']!=''){
                $picurl = parent::imgUpload();
                $data['picurl'] = $picurl;
            }*/
            $data['picurl'] = parent::uploadify() != '' ? parent::uploadify() : '';

            //将时间转化为时间戳
            $data['posttime'] = strtotime($_POST['posttime']);

            $result = M('infolist')->add($data);      //将所有数据添加进数据库

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
     * 文章修改页
     * @return [type] [description]
     */
    public function infolistUpdate(){
        $id = I('id', '', 'intval');   //文章id
        $infolist = M('infolist');      
        $infoclass = M('infoclass');

        //根据id查找文章信息
        $articleInfo = $infolist->where('id='.$id)->find();

        //查询所有栏目
        $infoclass = $infoclass->order('orderid asc')->select();
        //无限级分类
        import('Class.Infoclass');
        $classList = \Infoclass::infoclassForLevel($infoclass, '&nbsp;&nbsp;&nbsp;&nbsp;');

        //如果不是一级栏目，加上“|- ”
        foreach ($classList as $k => $v) {
            if($v['parentid'] != 0){
                $classList[$k]['html'] .= '|- ';
            }
        }

        //将时间戳转化为时间
        $articleInfo['posttime'] = date('Y-m-d H:i:s', $articleInfo['posttime']);

        //模板赋值
        $this->classList = $classList;
        $this->articleInfo = $articleInfo;

        //属性标记
        $flag = M('infoflag')->select();
        $this->flag = $flag;

        $time=date(DATE_RFC822);
        $this->assign('time',$time);
        $this->display();
    }

    /**
     * 文章修改方法
     * @return [type] [description]
     */
    public function infolistUpdateSave(){
        if(IS_POST){
            $id             = I('id', '', 'intval');            //文章id
            $classid        = I('classid', '', 'intval');      //所属栏目id

            //查询所属栏目父id
            $infoclass = M('infoclass')->field('parentid')->where('id='.$classid)->find();

            //文章属性
            $flag = I('flag');  
            if(is_array($flag))
            {
                $flag = implode(',', $flag);
            }

            $data = array();    //变量$data为数组
            $data = array(
                    'id'            =>   $id,
                    'classid'       =>   $classid,
                    'parentid'      =>   $infoclass['parentid'],
                    'title'         =>   I('title'),
                    'flag'          =>   $flag,
                    'keywords'      =>   I('keywords'),
                    'description'   =>   I('description'),
                    'content'       =>   I('content'),
                    'hits'          =>   I('hits', '', 'intval'),
                    'posttime'      =>   I('posttime'),
                    'orderid'       =>   I('orderid', '', 'intval'),
                    'checkinfo'     =>   I('checkinfo')
                );

            //获取图片路径
            /*if(isset($_FILES['picurl']['name']) && $_FILES['picurl']['name']!=''){
                $picurl = parent::imgUpload();
                $data['picurl'] = $picurl;
            }*/

            //将时间转化为时间戳
            $data['posttime'] = strtotime($data['posttime']);

            //判断是否有图片上传
            if(I('picurl') != ''){
                $data['picurl'] = I('picurl');     
            }

            M('infolist')->save($data);
        
            $this->success('修改成功！', U('index'));
        }else{
            $this->error('页面不存在！');
        }                 
    }

    /**
     * 删除文章
     * @return [type] [description]
     */
    public function delClass(){
        $id = I('id', '', 'intval');    //栏目id

        //删除文章
        $result = M('infolist')->where('id = '.$id)->delete();

        if($result){
            $this->success('删除成功！', U('index'));
        }else{
            $this->error('删除失败！');
        }
    }

    /**
     * 多选删除文章
     * @return [type] [description]
     */
    public function delAllClass(){
        $checkid = I('checkid');    //栏目id

        //删除文章
        foreach($checkid as $k=>$v)
        {     
            //删除文章
            $result = M('infolist')->where('id = '.$v)->delete();
        }
        if($result){
            $this->success('删除成功！', U('index'));
        }else{
            $this->error('删除失败！');
        }
    }

    /**
     * 修改文章状态
     * @return [type] [description]
     */
    public function check(){
        $id = I('id', '', 'intval');    //文章id

        $infoclass = M('infolist');  

        //查询栏目状态
        $classInfo = $infoclass->field('checkinfo')->where('id = '.$id)->find();
        if($classInfo['checkinfo'] == 'true'){
            //更新文章状态
            $infoclass->where('id = '.$id)->save(array('checkinfo'=>'false'));
        }else{
            //更新文章状态
            $infoclass->where('id = '.$id)->save(array('checkinfo'=>'true'));
        }

       // $this->success('更新成功！', U('index'));
      
    }  

    /**
     * 更新排序
     * @return [type] [description]  
     */
    public function updateOrderid(){
        $id = I('id', '', 'intval');       //文章id
        $orderid = I('orderid', '', 'intval');  //文章排序

        //更改排序
        $result = M('infolist')->where('id = '.$id)->save(array('orderid'=>$orderid));
        
        if($result){
            $data = M('infolist')->field('orderid')->where('id = '.$id)->find();
            return $data['orderid'];
        }
    }
   
}


