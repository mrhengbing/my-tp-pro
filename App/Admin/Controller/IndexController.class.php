<?php
/**
 * @Author: mrhengbing
 * @Email:  415671062@qq.com
 * --------------后台首页控制器----------------
 */

namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {
    /**
     * 后台模板
     * @return [type] [description]
     */
    public function index(){
        //获取session
        $admin = session();

        //查找管理员表
        $value = M('admin')->where('id='.$admin['uid'])->find();

        //判断昵称是否为空，如果为空则使用昵称，否则使用用户名
        if(!empty($value['nickname'])){
            $adminname = $value['nickname'];
        }else{
            $adminname = $admin['username'];
        }
        //本次登录时间
        $logintime = date('Y-m-d H:i:s', $value['logintime']);
        
        S('admin', $admin, 10);

        //模板赋值
        $this->admin = $admin;
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
        $this->display();
    }

    /**
     * 退出后台账号
     * @return [type] [description]
     */
    public function logout(){
        session(null);                      //清除session
        $this->redirect('Login/index');     //跳转到登陆界面
    }

}