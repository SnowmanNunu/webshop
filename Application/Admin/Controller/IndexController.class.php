<?php
namespace Admin\Controller;
use Think\Controller;
use Admin\Common\AdminController;

class IndexController extends AdminController {
    //集成页
    public function index(){ $this -> display();}

    public function top(){ $this -> display();}
    public function down(){ $this -> display();}
    public function center(){ $this -> display();}

    public function left(){ 
    	$admin_id = session('admin_id');
    	$admin_name = session('admin_name');

    	if ($admin_name !=='admin') {
    		//1) 普通管理员
            //sp_manager和sp_role做联表查询，获得sp_role表中的role_auth_ids信息
            $roleinfo = D('Manager')
                ->alias('m')
                ->join('__ROLE__ as r on m.role_id=r.role_id')
                ->where(array('m.mg_id'=>$admin_id))
                ->field('r.role_auth_ids')
                ->find();
                //SELECT r.role_auth_ids FROM sp_manager m INNER JOIN sp_role as r on m.role_id=r.role_id WHERE m.mg_id = '500'
            //dump($roleinfo);
            $authids = $roleinfo['role_auth_ids'];

            //获得auth权限信息
            $authinfoA = D('Auth')->where(array('auth_level'=>'0','auth_id'=>array('in',$authids)))->select();
            //SELECT * FROM `sp_auth` WHERE `auth_level` = '0' AND `auth_id` IN ('101','104','102','107')
            $authinfoB = D('Auth')->where(array('auth_level'=>'1','auth_id'=>array('in',$authids)))->select();
            //SELECT * FROM `sp_auth` WHERE `auth_level` = '1' AND `auth_id` IN ('101','104','102','107')
    	}else{
    		//2) 系统超级管理员
            //获得auth权限信息
            $authinfoA = D('Auth')->where(array('auth_level'=>'0'))->select();
            $authinfoB = D('Auth')->where(array('auth_level'=>'1'))->select();
    	}
    	$this -> assign('authinfoA',$authinfoA);
        $this -> assign('authinfoB',$authinfoB);
    	$this -> display();
    }

    public function right(){ $this -> display();}

}
