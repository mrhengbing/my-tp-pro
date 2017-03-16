<?php
/**
 * @Author: mrhengbing
 * @Create time:   2017-02-09 21:16:13
 * @Last Modified by:   mrhengbing
 * @Last Modified time: 2017-03-16 12:24:55
 * @Email:  415671062@qq.com
 * @---------后台首页控制器------------
 */
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {
    /**
     * 后台模板
     * @return [type] [description]
     */
    public function index(){

        //当前登陆管理员信息
        $adminInfo = parent::adminInfo();

        //判断昵称是否为空，如果为空则使用昵称，否则使用用户名
        if(!empty($adminInfo['nickname'])){
            $adminname = $adminInfo['nickname'];
        }else{
            $adminname = $adminInfo['username'];
        }
        //本次登录时间
        $logintime = date('Y-m-d H:i:s', $adminInfo['logintime']);
        
        S('admin', $adminInfo, 10);

        //模板赋值
        $this->admin = $adminInfo;
        $this->adminname = $adminname;
        $this->logintime = $logintime;

        $this->display();
    }

    /**
     * 后台左侧主菜单
     */
    public function leftMenu(){
        $this->display();
    }

    /**
     * 后台首页
     */
     public function home(){
        $model = new \Think\Model();                        //实例化Model类
        $ver = $model->query("select VERSION() as ver");    //查询数据库版本

        $serve = array();   //定义变量$serve为数组
        $serve = array(
                'is_imageline'      => function_exists('imageline'),  //判断GD库是否开启
                'mysqlver'          => $ver[0]['ver'],   //MySQL版本
                'is_zend'           => function_exists('zend_version'),  // 判断是否支持zend
                'upload_max'        => ini_get('upload_max_filesize')  //支持上传的最大文件
            );
        
        $this->serve = $serve;      //模板赋值

        //操作日志查询
        $this->adminlog = M('adminlog')->order('id desc')->limit(3)->select();

        $this->display();
    }

    /**
     * 退出后台账号
     * @return [type] [description]
     */
    public function logout(){
        parent::setLog();    //更新操作日志
        
        session(null);                      //清除session
        $this->redirect('Login/index');     //跳转到登陆界面
    }

}