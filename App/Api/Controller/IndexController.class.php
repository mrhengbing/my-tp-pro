<?php
/**
 * APP接口
 */
namespace Api\Controller;
use Think\Controller;

/**
 * 引入接口类
 */
import("Class.Api");

class IndexController extends Controller {
    public function index(){

        $arr = array(
                'id' => 2,
                'name'  => 'Tom',
                'age'   => 28
            );
  
        $test = new \Api();
        $test->show(200, '返回数据成功！', $arr);

    }
}