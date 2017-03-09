<?php
/**
 * @Author: mrhengbing
 * @Create time:   2017-02-09 11:16:13
 * @Last Modified by:   mrhengbing
 * @Last Modified time: 2017-03-09 18:01:28
 * @Email:  415671062@qq.com
 * @---------后台管理员模块控制器------------
 */
namespace Admin\Controller;
use Think\Controller;
class AdminController extends CommonController {
    /**
     * 权限验证
     */
    function _initialize(){
        $this->isModelAuth('admin');
    }
    
    /**
     * 管理员列表页
     * @return [type] [description]
     */
    public function index(){
        $admin         =   M('admin');      //实例化admin对象
        $admingroup    =   M('admingroup');   //实例化admingroup对象

        $count  = $admin->where('delstate=0')->count();// 查询满足要求的总记录数
        $Page   = getPage($count,20);// 传入总记录数和每页显示的记录数(25)
        $show   = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $admin->where('delstate=0')->order('id asc')->limit($Page->firstRow.','.$Page->listRows)->select();

        foreach ($list as $k=> $v) {
            //查找管理组名称
            $group = $admingroup->field('groupname')->where('id='.$v['levelname'])->find();
            $list[$k]['groupname'] =  $group['groupname'];
        }

        $this->list = $list;  //$this->assign('list',$list);// 赋值数据集
        $this->page = $show;  //$this->assign('page',$show);// 赋值分页输出
        $this->display();
    }
  
    /**
     * 管理员添加页
     * @return [type] [description]
     */
    public function adminAdd(){
        //查找管理组列表
        $levelname = M('admingroup')->where('checkinfo = true')->select();

        $this->levelname = $levelname;
        $this->display();
    }

    /**
     * 添加管理员方法
     * @return [type] [description]
     */
    public function adminAddSave(){
        if(IS_POST){
            $username   = I('username');     //用户名
            $password   = I('password');      //密码
            $levelname  = I('levelname');      //管理组id
            
            //只有超级管理员才有权创建超级管理员
            if(session('adminlevel') > 1 and $levelname == 1)
            {
                $this->error('非法的操作，不能创建超级管理员！');
            }
    
            //判断用户名是否合法
            if(preg_match("/[^0-9a-zA-Z_@!\.-]/",$username) ||
               preg_match("/[^0-9a-zA-Z_@!\.-]/",$password))
            {
                $this->error('用户名或密码非法！请使用[0-9a-zA-Z_@!.-]内的字符！');
            }
    
            //判断用户名是否存在
            $user = M('admin')->where('username="'.$username.'"')->find();
            if($user){
                $this->error('用户名已存在！');
            }
    
            $password = md5(md5($password));  
            $data = array(
                    'username'      => $username,    //用户名
                    'password'      => $password,    //密码
                    'nickname'      => I('nickname'), //昵称
                    'levelname'     => $levelname,      //管理组id
                    'loginip'       => '127.0.0.1',
                    'logintime'     => time()
                );
    
            if(M('admin')->add($data)){        //添加管理员
                $this->success('添加成功', U('index'));
            }else{
                $this->error('添加失败');
            }        
        }else{
            $this->error('页面不存在！');
        }

    }

    /**
     * 修改管理员页
     * @return [type] [description]
     */
    public function adminUpdate(){
        $id = I('id', '', 'intval');   //管理员id
        $admin = M('admin');      //实例化admin对象
        //根据id查找管理员信息
        $value = $admin->where('id='.$id)->find();
        //查找管理组列表
        $levelname = M('admingroup')->where('checkinfo = true')->select();
        
        //模板赋值
        $this->value = $value;
        $this->levelname = $levelname;

        $this->display();
    }

    /**
     * 更新管理员方法
     * @return [type] [description]
     */
    public function adminUpdateSave(){
        if(IS_POST){
            $id         = I('id', '', 'intval');   //管理员id
            $username   = I('username');          //用户名
            $password   = I('password');          //新密码
            $levelname  = I('levelname');       //管理组id

            //初始账号不允许更改状态
            if($id == 1 and $levelname != '1')
            {
                $this->error('抱歉，不能更改初始账号状态！');
            }

            //只有超级管理员才有权创建超级管理员
            if(session('adminlevel') > 1 and $levelname == 1)
            {
                $this->error('非法的操作，您不是超级管理员，不能创建超级管理员！');
            }

            $admin = M('admin');    //实例化admin对象
            //判断用户名是否存在       
            $user = $admin->where('username="'.$username.'" and id<>'.$id)->find();
            if($user){
                $this->error('用户名已存在！');
            }

            //管理员信息
            $data=array();
            $data = array(
                    'id'            => $id,         //管理员id
                    'username'      => $username,    //用户名
                    'nickname'      => I('nickname'), //昵称
                    'levelname'     => I('levelname'), //管理组id
                );

            //判断密码为空时不更新密码
            if ($password == '') {
                $admin->save($data);      //更新数据
                $this->success('修改成功！', U('index'));
            }else{
                $oldpwd     = I('oldpwd');          //接收旧密码
                $oldpwd     = md5(md5($oldpwd));    //密码两次md5加密
                $password   = md5(md5($password));
                //查询密码
                $r = $admin->field('password')->where('id='.$id)->find();
                //判断旧密码
                if($r['password'] != $oldpwd)
                {
                    $this->error('抱歉，旧密码错误！');
                }
                
                $data['password'] = $password;     //将新密码放进数组$data
                $admin->save($data);     //更新数据
                $this->success('修改成功！', U('index'));
            }
        }else{
            $this->error('页面不存在！');
        }     
    }

    /**
     * 删除管理员
     * @return [type] [description]
     */
    public function adminDelstate(){
        $id = I('id', '', 'intval');   //管理员id

        if(session('uid') == $id){
            $this->error("不能删除自己！");
        }

        $data['delstate'] = 1;
        $result = M('admin')->where('id='.$id)->save($data);
        //删除管理员
        if($result){
            $this->success('删除成功！', U('index'));
        }else{
            $this->error("删除失败！");
        }
    }

     /**
     * 管理员回收站列表页
     * @return [type] [description]
     */
    public function adminRecycle(){
        $admin = M('admin');   //实例化admin对象
        $admingroup    =   M('admingroup');   //实例化admingroup对象
        
        $count  = $admin->where('delstate=1')->count();// 查询满足要求的总记录数
        $Page   = getPage($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show   = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $admin->where('delstate=1')->order('id asc')->limit($Page->firstRow.','.$Page->listRows)->select();

        foreach ($list as $k=> $v) {
            //查找管理组名称
            $group = $admingroup->field('groupname')->where('id='.$v['levelname'])->find();
            $list[$k]['groupname'] =  $group['groupname'];
        }

        $this->list = $list;  //$this->assign('list',$list);// 赋值数据集
        $this->page = $show;  //$this->assign('page',$show);// 赋值分页输出

        $this->display();
    }

     /**
     * 还原管理员
     * @return [type] [description]
     */
    public function adminRestore(){
        $id = I('id', '', 'intval');   //管理员id
        $data['delstate'] = 0;
        $result = M('admin')->where('id='.$id)->save($data);
        //删除管理员
        if($result){
            $this->success('还原成功！', U('adminRecycle'));
        }else{
            $this->error("还原成功！");
        }
    }

    /**
     * 彻底删除管理员
     * @return [type] [description]
     */
    public function adminDel(){
        $id = I('id', '', 'intval');   //管理员id
        //彻底删除管理员
        if(M('admin')->delete($id)){
            $this->success('删除成功！', U('adminRecycle'));
        }else{
            $this->error("删除失败！");
        }
    }

    /**
     * 管理员组列表
     * @return [type] [description]
     */
    public function adminGroup(){
        $adminGroup = M('admingroup');   //实例化

        $count  = $adminGroup->count();// 查询满足要求的总记录数
        $Page   = getPage($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show   = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $value = $adminGroup->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->value = $value;
        $this->page = $show;  //$this->assign('page',$show);// 赋值分页输出
        $this->display();
    }

    /**
     * 管理组添加页
     * @return [type] [description]
     */
    public function adminGroupAdd(){
        //查找权限模块
        $this->authlist = M('authlist')->select();

        $this->display();
    }

    /**
     * 添加管理员方法
     * @return [type] [description]
     */
    public function adminGroupAddSave(){
        if(!IS_POST) $this->error("页面不存在！");

        $groupname = I('groupname');     //管理组名称

        $admingroup = M('admingroup');      //实例化admingroup对象

        //判断管理组名称是否存在
        $name = $admingroup->where('groupname="'.$groupname.'"')->find();
        if($name){
            $this->error('名称已存在！');
        }

        $data = array( 
                'groupname'     => $groupname,         //管理组名称
                'description'   => I('description'),    //管理组描述
                'checkinfo'     => I('checkinfo')      //管理组审核状态
            );

        $result = $admingroup->add($data);          //添加数据

        if($result){   
            $model = I('post.model');        //接收参数model
            if(isset($model) && is_array($model)){    //添加当前管理组的权限
                $lastid = $admingroup->field('id')->order('id desc')->find();
                foreach($model as $v){
                    $authlist = M('adminauth');
                    $data['groupid'] = $lastid['id'];
                    $data['model'] = $v;
                    $authlist->add($data);
                }
            }
            $this->success('添加成功', U('adminGroup'));
        }else{
            $this->error('添加失败');
        }
            

    }

    /**
     * 修改管理组页
     * @return [type] [description]
     */
    public function adminGroupUpdate(){
        $id = I('id', '', 'intval');   //管理员id
        $admingroup = M('admingroup');      //实例化admingroup对象
        //根据id查找管理员信息
        $value = $admingroup->where('id='.$id)->find();

        //查询权限模块
        $this->authlist = M('authlist')->select(); 

        //查询当前管理组的权限模块
        $admingroupModel = M('adminauth')->field('model')->where('groupid='.$id)->select();
        $adminModel = array();
        foreach($admingroupModel as $v){
            $adminModel[] = $v['model'];
        }
        $this->adminModel = $adminModel;

        //模板赋值
        $this->value = $value;

        $this->display();
    }

    /**
     * 更新管理组方法
     * @return [type] [description]
     */
    public function adminGroupUpdateSave(){
        if(!IS_POST) $this->error("页面不存在！");

        $id = I('id', '', 'intval');     //管理组id
        $groupname = I('groupname');     //管理组名称

        $admingroup = M('admingroup');    //实例化admingroup对象

        //判断名称是否存在       
        $name = $admingroup->where('groupname="'.$groupname.'" and id<>'.$id)->find();
        if($name){
            $this->error('名称已存在！');
        }

        //管理组信息
        $data=array();
        $data = array(
                'id'            => $id, 
                'groupname'     => $groupname,    //管理组名称
                'description'   => I('description'), //管理组描述
                'checkinfo'     => I('checkinfo')      //管理组审核状态
            );

        $result = $admingroup->save($data);      //更新数据
        
        M('adminauth')->where('groupid='.$id)->delete();     //删除原来的权限
        //添加新的权限
        $model = I('post.model');        //接收参数model
        if(isset($model) && is_array($model)){
            $lastid = $id;
            foreach($model as $v){
                $authlist = M('adminauth');
                $data['groupid'] = $lastid;
                $data['model'] = $v;
                $authlist->add($data);
            }
        }
        $this->success('修改成功！', U('adminGroup'));
           
    }

    /**
     * 删除管理组方法
     * @return [type] [description]
     */
    public function adminGroupDel(){
        $id = I('id', '', 'intval');   //管理员id

        //删除管理组
        if(M('admingroup')->delete($id)){
            $this->success('删除成功！', U('adminGroup'));
        }else{
            $this->error("删除失败！");
        }
    }

}