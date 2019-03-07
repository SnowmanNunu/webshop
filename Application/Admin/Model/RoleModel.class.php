<?php
namespace Admin\Model;
use Think\Model;

class RoleModel extends Model {
    //给角色更新权限
    function saveAuth($role_id,$auth_id){
        //① 维护role_auth_ids信息
        $auth_ids = implode(',',$auth_id); //Array--->String

        //② 维护role_auth_ac信息
        //根据$auth_ids查询对应的权限信息，以便获得权限里边的"控制器"和"操作方法"
        $authinfo = D('Auth')->where(array(
                'auth_level'=>array('gt','0'),
                'auth_id' => array('in',$auth_ids)
            ))->select();
        //SELECT * FROM `sp_auth` WHERE `auth_level` > '0' AND `auth_id` IN ('101','105','106','102','108','109')
        //遍历$authinfo非顶级权限信息，获得controller和action信息
        $s = array();
        foreach($authinfo as $k => $v){
            $s[] = $v['auth_c'].'-'.$v['auth_a'];
        }
        $ac = implode(',',$s); //Array--->String

        $arr = array(
            'role_id' => $role_id,
            'role_auth_ids' => $auth_ids,
            'role_auth_ac' => $ac
        );
        return $this -> save($arr);
        //UPDATE `sp_role` SET `role_auth_ids`='101,105,106,102,108,109',`role_auth_ac`='Goods-tianjia,Category-showlist,Order-dayin,Order-tianjia' WHERE `role_id` = 30
    }
}
