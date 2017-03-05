<?php
namespace Home\Controller;
use Think\Controller;
import('Class.Api');

class IndexController extends Controller {
    public function index(){
        $data = array("aa"=>11, "bb"=>22, "cc"=>33);
        $num = count($data);

        if(is_array($data) && !empty($data)){
      		return \Api::show(true, '数据返回成功！', $data, $num);
        }else{
        	return \Api::show(false, '数据返回错误');
        }       

    }
}