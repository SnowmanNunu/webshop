<?php
namespace Admin\Controller;
use Think\Controller;
use Admin\Common\AdminController;

class ManagerController extends AdminController {
    //登录后台系统
    public function login(){
        if(IS_POST){
            //验证码校验
            $code = I('post.manager_verify'); //获取用户输入验证码
            $vry = new \Think\Verify();
            if($vry -> check($code)){
                //校验用户名和密码
                $name = I('post.manager_name');
                $pwd = md5(I('post.manager_pwd'));
                //根据$name和$pwd为条件，去查询是否存在对应的管理员信息
                //返回:array || null
                $info = D('Manager')->where(array('mg_name'=>$name,'mg_pwd'=>$pwd))->find();
                if($info){
                    //持久化管理员信息
                    session('admin_id',$info['mg_id']);
                    session('admin_name',$info['mg_name']);
                    $this -> redirect('Index/index');//页面跳转
                }
                $this -> assign('errorlogin','用户名或密码错误');
            }else{
                $this -> assign('errorlogin','验证码错误');
            }
        }
        $this -> display();
    }


    //退出后台系统
    function logout(){
        session(null);//清空session
        $this -> redirect('Manager/login');//跳转到登录页
    }



        //生成验证码
    function verifyImg(){
        $cfg = array(
        'fontSize'  =>  20,              // 验证码字体大小(px)
        'useCurve'  =>  false,            // 是否画混淆曲线
        'useNoise'  =>  true,            // 是否添加杂点  
        'imageH'    =>  42,               // 验证码图片高度
        'imageW'    =>  150,               // 验证码图片宽度
        'length'    =>  4,               // 验证码位数
        'fontttf'   =>  '4.ttf',              // 验证码字体，不设置随机获取
        );
        $vry = new \Think\Verify($cfg);
        $vry -> entry();
    }



}
