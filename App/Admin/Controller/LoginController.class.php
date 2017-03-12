<?php
/**
 * @Author: mrhengbing
 * @Create time:   2017-02-09 8:35:10
 * @Last Modified by:   mrhengbing
 * @Last Modified time: 2017-03-12 16:46:59
 * @Email:  415671062@qq.com
 * @----------后台登陆模块控制器-------------
 */

namespace Admin\Controller;
use Think\Controller;

class LoginController extends CommonController {
    /**
     * 登陆页面视图引入
     */
    public function Index(){
        p(session('verify'));
        $this->display();
    }

    /**
     * 验证码
     */
    public function verify(){
        $config =    array(    
            'fontSize'    =>    16,    // 验证码字体大小    
            'length'      =>    4,     // 验证码位数    
            'useNoise'    =>    false, // 关闭验证码杂点
            );

        //实例化验证码类
        $Verify = new \Think\Verify($config);

        // 验证码字体使用 ThinkPHP/Library/Think/Verify/ttfs/5.ttf
        $Verify->fontttf = '5.ttf'; 

        // 设置验证码字符为纯数字
        $Verify->codeSet = '0123456789';
        //输出验证码 
        $Verify->entry();
    }

    /**
     * 登录方法
     * @return [type] [description]
     */
    public function login(){
        //判断参数传递方式是否为POST
        if(!IS_POST) $this->error('页面不存在！'); 

        //接收传递过来的参数
        $username = I("username");
        $password = I("password");
        $code     = I("code");

        $password = md5(md5($password));    //密码两次md5加密

        //根据用户名查找
        $user = M('admin')->where('username="'.$username.'" and delstate=0')->find();

        //判断用户名和密码
        if(!$user || $password != $user['password']){
            $this->error('用户名或密码错误！');
        }

        //判断验证码     
        if(!$this->checkVerify($code)){
            $this->error('验证码错误', 'index', 2);
        }

        //将用户信息保存至session
        session('uid', $user['id']);
        session('username', $user['username']);
        session('adminlevel', $user['levelname']);
        session('lastlogintime', date('Y-m-d H:i:s', $user['logintime']));
        session('loginip', $user['loginip']);

        //将登陆ip和登录时间保存进数组
        $data = array(
                'id' => $user['id'],
                'loginip' => get_client_ip(),
                'logintime' => time()
            );

        $this->setLog();    //更新操作日志
        
        //更新登陆ip和登录时间
        M('admin')->save($data); 

        //登陆成功后跳转至后台首页
        $this->redirect('Index/index');

    }

    /**
     * 检测输入的验证码是否正确，$code为用户输入的验证码字符串
     * @param  [type] $code [description]
     * @param  string $id   [description]
     * @return [type]       [description]
     */
    function checkVerify($code, $id = ''){    
        $verify = new \Think\Verify();          //实例化Verify类
        return $verify->check($code, $id);       //检测验证码，并返回结果
    }

}