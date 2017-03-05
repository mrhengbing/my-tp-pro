<?php
/**
 * @Author: mrhengbing
 * @Email:  415671062@qq.com
 * --------------后台公共控制器----------------
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
        if(!isset($_SESSION['uid']) || !isset($_SESSION['username'])){
            $this->redirect('Login/Index');
        }
    }

    
    /**
     * 图片上传
     * @return [type] [description]
     */
    public function uploadify()
    {
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
                //返回文件地址和名给JS作回调用
                return $picurl;
            }else{
                //$this->error($upload->getError());//上传失败
            }
        }
    }
}