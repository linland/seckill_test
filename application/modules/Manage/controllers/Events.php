<?php

/**
 * Created by ddt.
 * User: 谢林
 * Date: 2016/1/18 16:31
 * Description:
 */
class EventsController extends BasicController
{
    private $eventsModel,$goodsModel;
    public function init(){
        $this->eventsModel = $this->load('Events');
        $this->goodsModel = $this->load('Goods');
        $this->homeUrl = '/manage/events/index';
    }
    public function indexAction(){
        $eventsList = $this->eventsModel->getEventsList();
        $this->getView()->assign("eventsList",$eventsList);
        return true;
    }

    public function addAction(){
        if($this->isPost()){
            $eventsInfo = array();
            $eventsInfo['goods_id'] = $this->getPost('goodsId');
            $eventsInfo['num'] = abs(intval($this->getPost('goodsNum')));
            $eventsInfo['start_time'] = strtotime($this->getPost('startTime'));
            $eventsInfo['end_time'] = strtotime($this->getPost('endTime'));
            $fields = array('id','current_stock');
            $eventData = $this->goodsModel->getGoodsByID($fields,$eventsInfo['goods_id']);
            if(!$eventData){
                jsAlert('商品ID不存在');
            }
            if($eventsInfo['num'] > $eventData['current_stock']){
                jsAlert('商品库存不能超过当前剩余库存');
            }
            if($eventsInfo['start_time'] >= $eventsInfo['end_time']){
                jsAlert('结束时间必须晚于开始时间');
            }
            $this->eventsModel->beginTransaction();
            $eventsRow = $this->eventsModel->addEvents($eventsInfo);
            $goodsRow = $this->goodsModel->updateCurrentStockByGoodsID($eventsInfo['goods_id'],$eventData['current_stock']-$eventsInfo['num']);

            if(false !== $eventsRow && false !== $goodsRow){
                $this->eventsModel->Commit();
            }else{
                $this->eventsModel->Rollback();
                jsAlert('增加失败');
            }
            $this->goHome();
        }
        $goodsList = $this->goodsModel->getGoodsList();
        $this->getView()->assign("goodsList",$goodsList);
        return true;
    }

    public function editAction(){
        $id = $this->getParam('id');
        if(!is_numeric($id)){
            jsAlert("参数错误");
        }
        if($this->isPost()){
            $eventsInfo = array();
            $eventsInfo['goods_id'] = $this->getPost('goodsId');
            $eventsInfo['num'] = abs(intval($this->getPost('goodsNum')));
            $eventsInfo['start_time'] = strtotime($this->getPost('startTime'));
            $eventsInfo['end_time'] = strtotime($this->getPost('endTime'));
            $fields = array('id','current_stock');
            $eventData = $this->goodsModel->getGoodsByID($fields,$eventsInfo['goods_id']);
            if(!$eventData){
                jsAlert('商品ID不存在');
            }
            if($eventsInfo['num'] > $eventData['current_stock']){
                jsAlert('商品库存不能超过当前剩余库存');
            }
            if($eventsInfo['start_time'] >= $eventsInfo['end_time']){
                jsAlert('结束时间必须晚于开始时间');
            }

            $this->eventsModel->beginTransaction();
            //验证本次库存设置是否变动，大于原设置后，减少商品的当前库存,反之增加
            $oldEvents = $this->eventsModel->SelectByID(array('num'),$id);
            $storgeNum = 0;
            if($eventsInfo['num'] > $oldEvents['num']){
                $storgeNum = $eventData['current_stock'] - ($eventsInfo['num'] - $oldEvents['num']);
            }elseif($eventsInfo['num'] < $oldEvents['num']){
                $storgeNum = $eventData['current_stock'] + ($oldEvents['num'] - $eventsInfo['num']);
            }

            $goodsRow = $this->goodsModel->updateCurrentStockByGoodsID($eventsInfo['goods_id'], $storgeNum);

            $eventsRow = $this->eventsModel->updateEventsByID($id, $eventsInfo);

            if(false !== $eventsRow && false !== $goodsRow){
                $this->eventsModel->Commit();
            }else{
                $this->eventsModel->Rollback();
                jsAlert('修改失败');
            }
            $this->goHome();
        }
        $fields = array('id','goods_id','num','start_time','end_time');
        $eventsInfo = $this->eventsModel->SelectByID($fields,$id);
        $this->getView()->assign('eventsInfo',$eventsInfo);
        return true;
    }

    public function delAction(){
        $id = $this->getParam('id');
        if($id){
            $this->eventsModel->beginTransaction();
            //删除场次，将剩余数量恢复到总剩余库存中
            $oldEvents = $this->eventsModel->SelectByID(array('goods_id','num'),$id);

            if($oldEvents['num']){
                $fields = array('current_stock');
                $goodsData = $this->goodsModel->getGoodsByID($fields,$oldEvents['goods_id']);
                $this->goodsModel->updateCurrentStockByGoodsID($oldEvents['goods_id'], $goodsData['current_stock']+$oldEvents['num']);
            }

            $eventsRow = $this->eventsModel->delEventsByID($id);
            if($eventsRow){
                $this->eventsModel->Commit();
            }else{
                $this->eventsModel->Rollback();
                jsAlert('删除失败');
            }
        }
        $this->goHome();
    }
}