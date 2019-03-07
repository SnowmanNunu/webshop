<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller {
    //会员登录
    public function login(){
        $this -> display();
    }

    //会员注册
    public function regist(){
        $this -> display();
    }
}
