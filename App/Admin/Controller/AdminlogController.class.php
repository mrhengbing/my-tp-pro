<?php
/**
 * @Author: mrhengbing
 * @Create time:   2017-03-11 21:33:32
 * @Last Modified by:   mrhengbing
 * @Last Modified time: 2017-03-11 22:43:24
 * @Email:  415671062@qq.com
 * @-------管理员操作日志控制器--------
 */
namespace Admin\Controller;
use Think\Controller;
class AdminlogController extends CommonController {
	public function index(){
		$uname = I('get.uname');
		$starttime = I('get.starttime');
		$endtime = I('get.endtime');
		//搜索条件
		$where = 'id<>0';
		if(!empty($uname)){
			$where .= ' and uname="'.$uname.'"';
		}
		if(!empty($starttime) && !empty($endtime)){
			$starttime = strtotime($starttime);
			$endtime = strtotime($endtime);
			$where .= ' and posttime > '.$starttime.' and posttime < '.$endtime;
		}

		$adminlog = M('adminlog');
		$count  = $adminlog->where($where)->count();// 查询满足要求的总记录数
        $Page   = getPage($count, 20);// 传入总记录数和每页显示的记录数(25)
        $show   = $Page->show();// 分页显示输出

		$this->logList = $adminlog->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

		$this->page = $show;	//分页赋值

		$this->display();
	}
}