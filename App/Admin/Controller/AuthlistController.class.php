<?php
/**
 * @Author: mrhengbing
 * @Create time:   2017-03-09 14:16:13
 * @Last Modified by:   mrhengbing
 * @Last Modified time: 2017-03-11 21:35:46
 * @Email:  415671062@qq.com
 * @-------权限模块管理--------
 */
namespace Admin\Controller;
use Think\Controller;
class AuthlistController extends CommonController {
    /**
     * 初始化
     */
    function _initialize(){
        $this->isModelAuth('authlist');    //权限验证
        $this->setLog();    //更新操作日志
    }

    /**
     * 模块列表
     * @return [type] [description]
     */
    public function index(){
        $authlist   =   M('authlist');     
        $count      = $authlist->count();// 查询满足要求的总记录数
        $Page       = getPage($count,20);// 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $authlist->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->list = $list;  //$this->assign('list',$list);// 赋值数据集
        $this->page = $show;  //$this->assign('page',$show);// 赋值分页输出
        $this->display();
    }

    /**
     * 模块添加页
     * @return [type] [description]
     */
    public function authlistAdd(){
        $this->display();
    }

    /**
     * 添加模块方法
     * @return [type] [description]
     */
    public function authlistAddSave(){
        if(IS_POST){
            $data = I('post.');         //接收所有数据
            //检查是否重复
            $check = M('authlist')->where('model="'.$data['model'].'"')->find();
            if($check){
                $this->error('该模块已存在');
            }
            $result = M('authlist')->add($data);      //将所有数据添加进数据库
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
     * 模块修改页
     * @return [type] [description]
     */
    public function authlistUpdate(){
        $id = I('get.id');
        $authlist = M('authlist')->where('id='.$id)->find();

        $this->authlist = $authlist;
        $this->display();
    }

    /**
     * 修改模块方法
     * @return [type] [description]
     */
    public function authlistUpdateSave(){
        if(IS_POST){
            $data = I('post.');         //接收所有数据

            M('authlist')->save($data);      //更新数据库   
            $this->success('更新成功！', U('index'));    
        }else{
            $this->error('页面不存在！');
        }
    }

    /**
     * 删除方法
     * @return [type] [description]
     */
    public function authlistDel(){
        $id = I('get.id');   //模块id

        //删除管理组
        if(M('authlist')->delete($id)){
            $this->success('删除成功！', U('index'));
        }else{
            $this->error("删除失败！");
        }
    }

}