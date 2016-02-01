<?php
/**
 * @name GoodsModel
 * @desc 抢购商品模型
 * @author root
 */
class GoodsModel extends Model{

    function __construct(){
        $this->redis = Yaf_Registry::get('redis');
        $this->table = TB_PREFIX."goods";
        parent::__construct();
    }

    public function addGoods($goods){
        $row = $this->Insert($goods);
        if($row !== false){
            $this->redis->hmset("goods".$goods['goods_id'],$goods);
        }
        return $row;
    }

    public function delGoodsByID($id){
        $goodsID = $this->SelectByID('goods_id',$id);
        $row = $this->DeleteByID($id);
        if($row !== false){
            $this->redis->hdel("goods".$goodsID['goods_id']);
        }
        return $row;
    }

    public function updateGoodsByID($id,$data){
        $goods = array('goods_id'=>$data['goods_id'],'goods_name'=>$data['goods_name'],'goods_num'=>$data['goods_num']);
        $row = $this->UpdateByID($goods,$id);
        if($row !== false){
            $this->redis->hmset("goods".$data['goods_id'],$goods);
        }
        return $row;
    }

    public function getGoodsList($condition=array()){
        $field = array('id','goods_id','goods_name','current_stock','goods_num');
        return $this->Field($field)->Where($condition)->Select();
    }

    public function getGoodsByID($field,$goodsID){
        if(!$field){
            $field = array('id','goods_id','goods_name','current_stock','goods_num');
        }
        return $this->Field($field)->Where(array('goods_id'=>$goodsID))->SelectOne();
    }

    public function updateCurrentStockByGoodsID($goodsId,$stock){
        $condition = array('goods_id'=>$goodsId);
        $goods = array('current_stock'=>$stock);
        $row = $this->Where($condition)->UpdateOne($goods);
        if($row !== false){
            $this->redis->hset("goods".$goodsId,'current_stock',$stock);
        }
        return $row;
    }
}
