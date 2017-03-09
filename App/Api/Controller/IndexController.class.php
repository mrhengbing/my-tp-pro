<?php
/**
 * @Author: mrhengbing
 * @Create time:   2017-02-20 15:16:55
 * @Last Modified by:   mrhengbing
 * @Last Modified time: 2017-03-09 14:25:50
 * @Email:  415671062@qq.com
 * @---------接口控制器------------
 */
namespace Api\Controller;
use Think\Controller;
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