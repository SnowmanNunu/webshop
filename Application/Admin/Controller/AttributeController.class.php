<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
class AttributeController extends AdminController {
    //列表展示
    function showlist(){
        //获得属性列表信息
        // $info = D('Attribute')
        //     ->alias('a')
        //     ->join('__TYPE__ t on a.type_id=t.type_id')
        //     ->field('a.*,t.type_name')
        //     ->select();
            //SELECT a.*,t.type_name FROM sp_attribute a INNER JOIN sp_type t on a.type_id=t.type_id
        //$this -> assign('info',$info);

        //获取下拉列表展示的“商品类型”信息
        $typeinfo = D('Type')->select();
        $this -> assign('typeinfo',$typeinfo);

        $this -> display();
    }


    //根据类型type_id获得对应的属性列表信息
    function getAttrInfoByType(){
        if(IS_AJAX){
            //获取传递过来的类型type_id
            $type_id = I('get.type_id');

            //根据$type_id获取对应的属性列表信息
            if($type_id>0){
                //获得具体类型的属性信息
                $info = D('Attribute')
                    ->alias('a')
                    ->join('__TYPE__ t on a.type_id=t.type_id')
                    ->field('a.*,t.type_name')
                    ->where(array('a.type_id'=>$type_id))
                    ->select();
            }else{
                //获得"全部"的属性信息
                $info = D('Attribute')
                    ->alias('a')
                    ->join('__TYPE__ t on a.type_id=t.type_id')
                    ->field('a.*,t.type_name')
                    ->select();
            }       
            echo json_encode($info);
        }
    }
    
    
    //添加属性
    function tianjia(){
        $Attribute = D('Attribute');
        if(IS_POST){
            //$shuju = I('post.');
            //create()方法可以触发自动验证执行，如果返回false则说明验证失败
            $shuju = $Attribute->create();
            if($shuju!==false){
                //把可选值的"中文逗号" 替换为 "英文逗号"
                $shuju['attr_vals'] = str_replace('，',',',$shuju['attr_vals']);
                if($Attribute->add($shuju)){
                    $this -> success('添加属性成功',U('showlist'),2);
                }else{
                    $this -> error('添加属性失败',U('tianjia'),2);
                }
            }else{
                //验证出现问题，把错误信息传递给模板展示
                //getError()会把验证的错误信息通过关联数组形式返还
                //array('attr_name'=>'属性名称必须设置','type_id'=>'商品类型必须选取')
                $this -> assign('errorinfo',$Attribute->getError());
            }
        }
        //获取可供选取的商品属性信息
        $typeinfo = D('Type')->select();
        $this -> assign('typeinfo',$typeinfo);

        $this -> display();

    }
}
