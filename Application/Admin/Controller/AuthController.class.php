<?php
namespace Admin\Controller;
use Think\Controller;
use Admin\Common\AdminController;

class AuthController extends AdminController {
    //列表展示
    function showlist(){
        //获得权限列表信息
        $info = D('Auth')->select();
        //对$info进行递归分类排序处理
        $info = generateTree($info);
        //dump($info);

        $this -> assign('info',$info);

        $this -> display();
    }


    //添加权限
    function tianjia(){
        $auth = D('Auth');
        if(IS_POST){
            //收集信息
            //dump($_POST);
            $data = I('post.');

            //对auth_level进行特殊维护处理
            $data['auth_level'] = $data['auth_pid']=='0'?'0':'1';

            if($auth -> add($data)){
                $this -> success('添加权限成功',U('showlist'),2);
            }else{
                $this -> error('添加权限失败',U('tianjia'),2);
            }
        }else{
            //展示表单
            //获得可供选取的上级权限,只获取"顶级"权限即可
            $authinfo = $auth->where(array('auth_level'=>'0'))->select();
            $this -> assign('authinfo',$authinfo);

            $this -> display();
        }
    }


}
