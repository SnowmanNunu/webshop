<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    //展示首页面
    public function index(){
        $this -> display();
    }
}