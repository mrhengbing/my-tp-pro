<?php
/**
 * @Author: mrhengbing
 * @Create time:   2017-02-15 21:12:13
 * @Last Modified by:   mrhengbing
 * @Last Modified time: 2017-03-11 21:36:02
 * @Email:  415671062@qq.com
 * @----------文章属性模块控制器-------------
 */
namespace Admin\Controller;
use Think\Controller;
class InfoflagController extends CommonController {
    /**
     * 初始化
     */
    function _initialize(){
        $this->isModelAuth('infoflag');    //权限验证
        $this->setLog();    //更新操作日志
    }

    /**
     * 信息标记页
     * @return [type] [description]
     */
    public function index(){
        $flagNum = M('infoflag')->count();      //总记录数
        $infoflag = M('infoflag')->select();    //所有标记

        /*查询最大的排序*/
        $orderid = M('infoflag')->field('orderid')->order('orderid desc')->find();

        /*新建栏目默认排序为数据表中最大排序+1*/
        $this->orderid = $orderid['orderid']+1;

        /*模板赋值*/
        $this->flagNum = $flagNum;          

        $this->infoflag = $infoflag;

        $this->display();
    }

    /**
     * 属性更新
     * @return [type] [description]
     */
    public function flagUpdate(){
        if(IS_POST){
            $id             =   I('id');
            $flag           =   I('flag');
            $flagname       =   I('flagname');
            $orderid        =   I('orderid');

            $flagnameadd    =   I('flagnameadd');
            $flagadd        =   I('flagadd');
            $orderidadd     =   I('orderidadd', '', 'intval');

            $dataAdd = array();
            /*判断有新属性，则添加进数据库*/
            if($flagnameadd != '' && $flagadd != ''){
                $dataAdd = array(
                    'flag'      =>  $flagadd,
                    'flagname'  =>  $flagnameadd,
                    'orderid'   =>  $orderidadd
                );
                M('infoflag')->add($dataAdd);
            }

            $ids = count($id);  
            
            for($i = 0; $i < $ids; $i++){
                $data = array(
                    'flag'=>$flag[$i], 
                    'flagname'=>$flagname[$i], 
                    'orderid'=>$orderid[$i]
                    );
                $condition['id'] = $id[$i];
               M('infoflag')->where($condition)->setfield($data);
            }
            $this->success('更新完成！', U('index'));
        }else{
            $this->error('页面不存在！');
        }  
    }

    /**
     * 删除属性
     * @return [type] [description]
     */
    public function flagDelete(){
        $id = I('id', '', 'intval');    //属性id

        //删除属性
        $result = M('infoflag')->where(array('id'=>$id))->delete();

        if($result){
            $this->success('删除成功！', U('index'));
        }else{
            $this->error('删除失败！');
        }
    }

    /**
     * 多选删除属性
     * @return [type] [description]
     */
    public function delAllFlag(){
        $checkid = I('checkid');    //属性id
        
        //删除属性
        foreach($checkid as $k=>$v)
        {     
            //删除属性
            $result = M('infoflag')->where('id = '.$v)->delete();
        }
        if($result){
            $this->success('删除成功！', U('index'));
        }else{
            $this->error('删除失败！');
        }
    }

    /**
     * 更新排序
     * @return [type] [description]  
     */
    public function updateOrderid(){
        $id = I('id', '', 'intval');       //属性id
        $orderid = I('orderid', '', 'intval'); 

        //更改排序
        $result = M('infoflag')->where('id = '.$id)->save(array('orderid'=>$orderid));
        
        if($result){
            $data = M('infoflag')->field('orderid')->where('id = '.$id)->find();
            return $data['orderid'];
        }

    }

}