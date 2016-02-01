<?php

/**
 * Created by ddt.
 * User: 谢林
 * Date: 2016/1/18 16:31
 * Description:
 */
class GoodsController extends BasicController
{
    private $goodsModel;
    public function init(){
        $this->goodsModel = $this->load('Goods');
        $this->homeUrl = '/manage/goods/index';
    }
    public function indexAction(){
        $goodsList = $this->goodsModel->getGoodsList();
        $this->getView()->assign("goodsList",$goodsList);
        return true;
    }

    public function addAction(){
        if($this->isPost()){
            $goodsInfo = array();
            $goodsInfo['goods_id'] = $this->getPost('goodsId');
            $goodsInfo['goods_name'] = $this->getPost('goodsName');
            $goodsInfo['goods_num'] = intval($this->getPost('goodsNum'));
            $goodsInfo['current_stock'] = $goodsInfo['goods_num'];
            $goodsInfo['create_time'] = CUR_TIMESTAMP;
            $row = $this->goodsModel->addGoods($goodsInfo);
            if(false === $row){
                jsAlert('增加失败');
            }
            $this->goHome();
        }
        return true;
    }

    public function editAction(){
        $id = $this->getParam('id');
        if(!is_numeric($id)){
            jsAlert("参数错误");
            $this->goHome();
        }
        if($this->isPost()){
            $goodsInfo = array();
            $goodsInfo['goods_id'] = $this->getPost('goodsId');
            $goodsInfo['goods_name'] = $this->getPost('goodsName');
            $goodsInfo['goods_num'] = intval($this->getPost('goodsNum'));
            $row = $this->goodsModel->updateGoodsByID($id,$goodsInfo);
            if(false === $row){
                jsAlert('修改失败');
            }
            $this->goHome();
        }
        $fields = array('id','goods_id','goods_name','goods_num');
        $goodsInfo = $this->goodsModel->SelectByID($fields,$id);
        $this->getView()->assign('goodsInfo',$goodsInfo);
        return true;
    }

    public function delAction(){
        $id = $this->getParam('id');
        if($id){
            $row = $this->goodsModel->delGoodsByID($id);
        }
        if(false === $row){
            jsAlert('删除失败');
        }
        $this->goHome();
    }
}