<?php
namespace Admin\Controller;
use Think\Controller;
use Admin\Common\AdminController;

class GoodsController extends AdminController {
    //商品列表
    public function showlist(){ 
        //$obj = new \Admin\Model\GoodsModel();//Admin\Model\GoodsModel
        //$obj = D('Goods');//Admin\Model\GoodsModel
        //$obj = M('Goods'); //Think\Model
        $info = D('Goods')->order('goods_id desc')->select();
        //dump($info);
        $this->assign('info',$info);
        $this -> display();
    }

    //添加商品
    public function tianjia(){

        $goods = D('Goods'); 
        if (IS_POST) {
        // dump($_POST);exit;
        //给商品实现logo图片上传
        $this -> deal_logo();
        $data = I('post.');
        $data['add_time'] = $data['upd_time'] = time();
        $data['goods_introduce'] = \fangXSS($_POST['goods_introduce']);

        if($newid = $goods->add($data)){
                //相册维护
                $this -> deal_pics($newid);
                //属性信息维护
                $this->deal_attr($newid);
                $this -> success('添加商品成功',U('showlist'),2);
            }else{
                $this -> error('添加商品失败',U('tianjia'),2);
            }
        }else{            
            //展示表单
            //获取类型信息
            $typeinfo = D('Type')->select();
            $this -> assign('typeinfo',$typeinfo);

            $data = I('post.');
            $this -> display();
        }
    }

    //添加商品实现属性信息的维护(sp_goods_attr表)
    private function deal_attr($goods_id){
        //如果是修改商品，维护属性信息，则要删除旧属性
        D('GoodsAttr')->where(array('goods_id'=>$goods_id))->delete();
        foreach($_POST['attr_info'] as $k => $v){
            //$k是属性id值
            foreach($v as $vv){
                if(!empty($vv)){
                    $arr['goods_id'] = $goods_id;
                    $arr['attr_id'] = $k;
                    $arr['attr_value'] = $vv;
                    //给关联表sp_goods_attr填充数据
                    //D('GoodsAttr')对应数据表sp_goods_attr
                    //D('Goodsattr')对应数据表sp_goodsattr
                    D('GoodsAttr')->add($arr);
                }
            }
        }
    }

    //根据类型获得属性信息[添加商品]
    function getAttrByType(){
        //获得客户端传递过来的type_id
        $type_id = I('get.type_id');

        //根据$type_id获得对应的属性信息
        $attrinfo = D('Attribute')
            ->where(array('type_id'=>$type_id))
            ->select();
        echo json_encode($attrinfo);
        //[{"attr_id":"1","attr_name":"cpu","type_id":"1","attr_sel":"only","attr_write":"manual","attr_vals":""},{"attr_id":"2","attr_name":"\u5916\u89c2\u6837\u5f0f","type_id":"1","attr_sel":"many","attr_write":"list","attr_vals":"\u7ffb\u76d6,\u6ed1\u76d6,\u76f4\u677f,\u6298\u53e0"},{"attr_id":"3","attr_name":"\u5185\u5b58\u5bb9\u91cf","type_id":"1","attr_sel":"only","attr_write":"manual","attr_vals":""},{"attr_id":"4","attr_name":"\u5c4f\u5e55\u5927\u5c0f","type_id":"1","attr_sel":"many","attr_write":"list","attr_vals":"5.0\u82f1\u5bf8,5.5\u82f1\u5bf8,6.0\u82f1\u5bf8"}]
    }

    //根据类型获得属性信息[修改商品]
    function getAttrByType2(){
        //获取客户端传递过来的goods_id和type_id
        $goods_id = I('get.goods_id');
        $type_id = I('get.type_id');

        //获得属性列表信息(实体、空壳)
        //sp_attribue 与 sp_goods_attr做联表查询 通过attr_id关联
        //保证sp_attribute属性表的数据是完整的，如果sp_goods_attr关联表有对应数据则一并查出
        $attrinfo = D('Attribute')
            ->alias('a')
            ->field('a.attr_id,a.attr_name,a.attr_sel,a.attr_vals,(select group_concat(ga.attr_value) from sp_goods_attr ga where ga.attr_id=a.attr_id and ga.goods_id='.$goods_id.') attr_values')
            ->where(array('a.type_id'=>$type_id))
            ->select();
        //SELECT a.attr_id,a.attr_name,a.attr_sel,a.attr_vals,(select group_concat(ga.attr_value) from sp_goods_attr ga where ga.attr_id=a.attr_id and ga.goods_id=20) attr_values FROM sp_attribute a WHERE a.type_id = '1'
        echo json_encode($attrinfo);
        //[{"attr_id":"7","attr_name":"\u56fd\u5bb6","attr_sel":"only","attr_vals":"","attr_values":null},{"attr_id":"8","attr_name":"\u65f6\u957f","attr_sel":"only","attr_vals":"","attr_values":null}]
    }


    //实现商品logo图片上传处理
    //$goods_id:为0 表示是新增商品logo处理
    //$goods_id:非0 表示是修改商品logo处理
    private function deal_logo($goods_id=0){
        //给商品实现logo图片上传
        //dump($_FILES);
        if($_FILES['goods_logo']['error']===0){
            //修改商品时，要把该商品原先的logo物理图片文件给删除-start
            if($goods_id!==0){
                $goodsinfo = D('Goods')->find($goods_id);
                if(file_exists($goodsinfo['goods_big_logo'])){
                    unlink($goodsinfo['goods_big_logo']);
                }                
                if(file_exists($goodsinfo['goods_small_logo'])){
                    unlink($goodsinfo['goods_small_logo']);
                }
            }
            //修改商品时，要把该商品原先的logo物理图片文件给删除-end

            //① 上传logo图片
            //有正常上传附件
            $cfg = array(
                'rootPath'      =>  './Public/Uploads/logo/', //保存根路径
            );
            $up = new \Think\Upload($cfg);
            //uploadOne()方法会返回附件的上传子目录 和 名字信息
            $z = $up -> uploadOne($_FILES['goods_logo']);
            //dump($z);

            //把上传好的附件存储给数据库,具体存储附件路径名
            //./Public/Uploads/logo/2018-02-09/589be3664197e.jpg
            $_POST['goods_big_logo'] = $up->rootPath.$z['savepath'].$z['savename'];

            //② 对logo图片制作缩略图
            $im = new \Think\Image();//创建对象
            $im -> open($_POST['goods_big_logo']);//找到被处理原图并打开
            $im -> thumb(130,130,6);//制作缩略图，严格缩放大小为130*130
            //制作好的缩略图存储到服务器
            //./Public/Uploads/logo/2018-02-09/small_589be3664197e.jpg
            $smallPathName = $up->rootPath.$z['savepath'].'small_'.$z['savename'];
            $im -> save($smallPathName);

            //缩略图存储到数据库中
            $_POST['goods_small_logo'] = $smallPathName;
        }
    }




    //实现相册上传维护
    private function deal_pics($goods_id){
        //判断是否有上传相册(至少有一个也可以)
        $havePics = false;
        foreach($_FILES['goods_pics']['error'] as $v){
            if($v===0){
                $havePics = true;
                break;
            }
        }

        //有上传相册才处理
        if($havePics === true){
            //dump($_FILES);
            $cfg2 = array(
                'rootPath'      =>  './Public/Uploads/pics/', //保存根路径
            );
            $up2 = new \Think\Upload($cfg2);

            //相册批量上传处理，upload(二维数组)
            $z2 = $up2 -> upload(array($_FILES['goods_pics']));
            //dump($z2);

            //给相册制作缩略图，遍历$z2获得每个已经上传好的相册图片
            $im2 = new \Think\Image();
            foreach($z2 as $k => $v){
                //获得原相册路径名  2018-02-09/589c348a870a9.jpg
                $yuan_pics = $up2->rootPath.$v['savepath'].$v['savename'];

                $im2 -> open($yuan_pics);//打开原图
                //缩略图范围：800*800   350*350   50*50
                //同一个原图可以同时制作多个大小不同的缩略图
                //要求：缩略图由大到小的顺序制作

                $im2 -> thumb(800,800,6);
                //2018-02-09/big_589c348a870a9.jpg
                $pics_big = $up2->rootPath.$v['savepath'].'big_'.$v['savename'];
                $im2 -> save($pics_big);//存储缩略图

                $im2 -> thumb(350,350,6);
                //2018-02-09/mid_589c348a870a9.jpg
                $pics_mid = $up2->rootPath.$v['savepath'].'mid_'.$v['savename'];
                $im2 -> save($pics_mid);//存储缩略图    

                $im2 -> thumb(50,50,6);
                //2018-02-09/sma_589c348a870a9.jpg
                $pics_sma = $up2->rootPath.$v['savepath'].'sma_'.$v['savename'];
                $im2 -> save($pics_sma);//存储缩略图

                //删除无用的原图
                unlink($yuan_pics);

                //把缩略图相册存储给数据库
                $arr = array(
                    'goods_id'=>$goods_id,
                    'pics_big'=>$pics_big,
                    'pics_mid'=>$pics_mid,
                    'pics_sma'=>$pics_sma,
                );
                D('GoodsPics')->add($arr);
            }
        }
    }


    //修改商品
    public function upd(){ 
        if (IS_POST) {
        $goods_upd_id = session('goods_upd_id');
            if ($goods_upd_id === $_POST['goods_id']) {

            //商品logo图片修改处理
            $this -> deal_logo($_POST['goods_id']);
            //商品相册图片上传处理
            $this -> deal_pics($_POST['goods_id']);
            //实现属性信息收集入库
            $this -> deal_attr($_POST['goods_id']);
            $data = I('post.');
            $data['upd_time'] = time();
            $data['goods_introduce'] = \fangXSS($_POST['goods_introduce']);
            if(D('Goods')->save($data)){
                $this -> success('修改商品成功',U('showlist'),2);
            }else{
                $this -> error('修改商品失败',U('upd',array('goods_id'=>$data['goods_id'])),2);
            }
        }else{
                //upd.html修改商品的隐藏域goods_id有被动手脚
                $this->error('参数有问题，请联系管理员',U('showlist'),3);
        }
        }else{

        //接收被修改商品的id值
        $goods_id = I('get.goods_id');
        //把当前被修改的商品id信息存储给session,用于后期比较
        session('goods_upd_id',$goods_id);
        //根据$goods_id获得被修改商品的基本信息并传递给模板
        $info = D('Goods')->find($goods_id);
        $this -> assign('info',$info);     
        //获取被修改商品的相册，并传递给模板
        $picsinfo = D('GoodsPics')->where(array('goods_id'=>$goods_id))->select();
        $this -> assign('picsinfo',$picsinfo);
        $this -> display();
            
        }
    }


        //删除相册图片
    function delPics(){
        $pics_id = I('post.pics_id');//接收pics_id

        //根据$pics_id做条件进行相册查询
        $picsinfo = D('GoodsPics')->find($pics_id);
        //删除物理相册图片
        if(file_exists($picsinfo['pics_big'])){unlink($picsinfo['pics_big']);}
        if(file_exists($picsinfo['pics_mid'])){unlink($picsinfo['pics_mid']);}
        if(file_exists($picsinfo['pics_sma'])){unlink($picsinfo['pics_sma']);}

        //删除数据记录
        if(D('GoodsPics')->delete($pics_id)){  
            echo json_encode(array('status'=>0)); //成功
        }else{
            echo json_encode(array('status'=>1)); //失败
        }
    }


}
