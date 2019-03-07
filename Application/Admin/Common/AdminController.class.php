<?php
namespace Admin\Common;
use Think\Controller;

class AdminController extends Controller{
    //构造方法
    function __construct(){
        parent::__construct();//避免覆盖父类构造方法，先执行
        //控制管理员越权访问
        $admin_id   = session('admin_id');
        $admin_name = session('admin_name');
        //当前访问的控制器-操作方法
        //MODULE_NAME:分组名称 //CONTROLLER_NAME:控制器名称 //ACTION_NAME:操作方法名称 
        $nowAC = CONTROLLER_NAME."-".ACTION_NAME;  //Goods-showlist
        //判断用户是否有登录系统
        if(empty($admin_name)){
            //1) 没有登录系统
            $allow_auth = "Manager-login,Manager-verifyImg,Manager-logout";
            //如果用户访问非法权限，则做登录跳转
            if(strpos($allow_auth,$nowAC)===false){
                //redirect会造成只是right右侧提示登录窗口
                //$this -> redirect('Manager/login');  
                $js = <<<eof
                    <script type="text/javascript">
                        window.top.location.href="/index.php/Admin/Manager/login";
                    </script>
eof;
                echo $js;
            }
        }else{
            //2) 有登录系统
            //获得当前管理员角色的权限信息
            $roleinfo = D('Manager')
                ->alias('m')
                ->join('__ROLE__ as r on m.role_id=r.role_id')
                ->field('r.role_auth_ac')
                ->where(array('m.mg_id'=>$admin_id))
                ->find();
                //SELECT r.role_auth_ac FROM sp_manager m INNER JOIN sp_role as r on m.role_id=r.role_id WHERE m.mg_id = '500' 
            //dump($roleinfo);
            //Goods-tianjia,Category-showlist,Order-dayin,Order-tianjia
            $have_auth = $roleinfo['role_auth_ac'];

            //系统默认允许访问的权限(无需分配)
            $allow_auth = "Manager-login,Manager-logout,Manager-verifyImg,Index-top,Index-left,Index-center,Index-down,Index-right,Index-index";

            //判断：
            //① 判断当前访问的权限 是否在拥有的权限里边存在
            //② 判断当前访问的权限 是否是默认允许访问的
            //③ 判断当前用户 是否是系统管理员admin
            //以上①、②、③如果都是否定的，则就是越权访问
            //strpos($s1,$s2),判断$s2小串内容在$s1里边左数第几个位置有出现，返回位置数目，数目从0开始计数
            //如果没有出现要返回false
            //例如：strpos('helloworld','my')
            if(strpos($have_auth,$nowAC)===false && 
                strpos($allow_auth,$nowAC)===false &&
                $admin_name!=='admin'){
                exit('没有权限访问！');
            }
        }
    }
}
