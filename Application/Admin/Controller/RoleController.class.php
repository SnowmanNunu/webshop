<?php
namespace Admin\Controller;
use Think\Controller;
use Admin\Common\AdminController;

class RoleController extends AdminController {
    //列表展示
    function showlist(){
        //获得角色列表信息
        $info = D('Role')->select();
        $this -> assign('info',$info);

        $this -> display();
    }

    //分配权限
    function distribute(){
        $role = D('Role');
        if(IS_POST){
            $role_dis_id = session('role_dis_id');
            //判断form表单的role_id隐藏域信息没有被认为篡改
            if($role_dis_id===$_POST['role_id']){
                //① 收集表单信息入库
                //dump($_POST);
                $z = $role -> saveAuth($_POST['role_id'],$_POST['auth_id']); //给角色更新权限

                if($z){
                    $this -> success('分配权限成功',U('showlist'),2);
                }else{
                    $this -> error('分配权限失败',U('distribute',array('role_id'=>$_POST['role_id'])),2);
                }
                
            }else{
                $this -> error('相关参数出问题，请联系管理员',U('showlist'),2);
            }
        }else{
            //② 展示表单
            //获得被分配权限角色的role_id，并进一步获得该角色的详情信息
            $role_id = I('get.role_id');
            $roleinfo = $role->find($role_id);
            $this -> assign('roleinfo',$roleinfo);

            //把被分配权限的角色role_id存储给session
            session('role_dis_id',$role_id);

            //把可用于分配的权限信息获得出来并传递给模板展示
            //分别获取父、子级权限
            $authinfoA = D('Auth')->where(array('auth_level'=>'0'))->select();
            $authinfoB = D('Auth')->where(array('auth_level'=>'1'))->select();
            $this -> assign('authinfoA',$authinfoA);
            $this -> assign('authinfoB',$authinfoB);

            $this -> display();
        }
    }
}
