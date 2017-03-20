<?php
/**
 * @Author: mrhengbing
 * @Create time:   2017-02-09 10:16:13
 * @Last Modified by:   mrhengbing
 * @Last Modified time: 2017-03-21 00:04:38
 * @Email:  415671062@qq.com
 * @---------后台公共控制器------------
 */
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller {
    /**
     * 构造方法
     * @return [type] [description]
     */
    public function _initialize(){
        //自动运行方法
        if((!isset($_SESSION['uid']) || !isset($_SESSION['username'])) && CONTROLLER_NAME!='Login'){
            $this->redirect('Login/Index');
        }
    }

    /**
     * 获取当前管理员信息
     * @return [type] [description]
     */
    static public function adminInfo(){
            //获取session
            $adminSession = session();

            //查找管理员表
            $admin = M('admin');
            $condition['id'] = $adminSession['uid'];
            $adminInfo = $admin->where($condition)->find();

            return $adminInfo;
    }

    /**
     * 图片普通上传处理
     * @return [type] [description]
     */
    public function imgUpload(){
         if (!empty($_FILES)) {
            //图片上传设置
            $config = array(   
                'savePath'   =>    '',  
                'saveName'   =>    array('uniqid',''), 
                'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),  
                'autoSub'    =>    true,   
                'subName'    =>    array('date','Ymd'),
            );
            $upload = new \Think\Upload($config);// 实例化上传类
            $images = $upload->upload();
            //判断是否有图
            if($images){
                $picurl = $images['picurl']['savepath'].$images['picurl']['savename'];
                //返回文件地址和名
                return $picurl;
            }else{
                //$this->error($upload->getError());//上传失败
            }
        }
    }

    /**
     * 图片异步上传处理
     * @return [type] [description]
     */
   public function uploadify(){
        if (!empty($_FILES)) {
            //图片上传设置
            $config = array(   
                'savePath'   =>    '',  
                'saveName'   =>    array('uniqid',''), 
                'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),  
                'autoSub'    =>    true,   
                'subName'    =>    array('date','Ymd'),
            );
            $upload = new \Think\Upload($config);// 实例化上传类
            $images = $upload->upload();
            //判断是否有图
            if($images){
                $picurl = 'Uploads/'.$images['Filedata']['savepath'].$images['Filedata']['savename'];
                //返回文件地址和名
                echo $picurl;
            }else{
                //$this->error($upload->getError());//上传失败
            }
        }
    }


    public function del(){
        if($_POST['name']!=""){
            $info = explode("/", $_POST['name']);
            //count($info)
            $url='./Public/upload/'.$info[count($info)-1];
            if(unlink($url)){
                $this->success("success");
            }
            else
                $this->error("unlink fail");
            }
        else
            $this->error("info is gap");
    }
    

    /**
     * 验证模块权限
     * @param string $m [模型名]
     */
    protected function isModelAuth($m=''){
        $admin = $this->adminInfo();         //当前管理员的信息
        $adminlevel = $admin['levelname'];     //当前管理员的组id

        //超级管理员拥有所有模块的权限
        if($cfg_adminlevel == 1)
        {
            return true;
        }

        //非超级管理员判断权限
        if($adminlevel != 1)
        {
            $adminauth = M('adminauth');
            $condition['groupid'] = $adminlevel;
            $condition['model']   = $m;

            $result = $adminauth->where($condition)->find();
          
            if(isset($result) && is_array($result)){
                return true;
            }else{
                $this->error('你无权操作此模块！');
            }
        }else{
            return false;
        }      
    }

    /**
     * 更新操作日志
     */
    public function setLog(){
        //操作日志的数据
        $data = array(
                'uname'     =>  $_SESSION['username'],
                'model'     =>  CONTROLLER_NAME,
                'action'    =>  ACTION_NAME,
                'posttime'  =>  time(),
                'ip'        =>  get_client_ip()
            );

        $adminlog = M('adminlog');
        $condition['uname'] = $_SESSION['username'];
        $condition['model'] = CONTROLLER_NAME;
        //当前模型最近的一次操作
        $log = $adminlog->field('posttime')->where($condition)->order('id desc')->find();
        //更新操作日志
        //一分钟内连续操作只记录一次
        if(!isset($log['posttime'])){
            $adminlog->add($data);
        }elseif(isset($log['posttime']) && ($log['posttime']<(time()-60))){
            $adminlog->add($data);
        }
            
    }

}