<?php
/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends BasicController {

    private $redis,$event;

    public function init(){
        $this->redis = Yaf_Registry::get('redis');
        $this->homeUrl = '/index/index/index';
        $this->event = $this->load("Events");
    }

    /**
     * 抢购列表
     * @return bool
     */
	public function indexAction() {
        //只显示当天抢购场次
        $day = strtotime(date('Y-m-d'));
        $dayEnd = $day + 86400;
//        两天内的抢购场次
//        $TwoDay = strtotime(date('Y-m-d',strtotime('+2 day')));


        $eventsLength = $this->redis->llen("eventID");

        $currentEvents = array();
        for($i=0;$i<$eventsLength;$i++){
            $eventID = $this->redis->lget("eventID",$i);
            $event = $this->redis->hGetAll("events".$eventID);
            if($event['start_time'] > $day && $event['start_time'] < $dayEnd){
                $goods = $this->redis->hGetAll("goods".$event['goods_id']);
                $currentEvents[] = array_merge($event,$goods,array('event_id'=>$eventID));
            }
        }
        var_dump($currentEvents);
        $this->getView()->assign("eventsList",$currentEvents);
        return true;
	}


    public function seckillAction(){
        $eId = $this->getParam('eid');
        $reqTime = $_SERVER['REQUEST_TIME'];
        $eventInfo = $this->event->checkEventDate($eId,$reqTime);
        if($eventInfo == -1){
            jsAlert('该场抢购过期了，下次再来吧!');
        }elseif($eventInfo == -2){
            jsAlert('抢购还未开始，再等等吧!');
        }elseif($eventInfo == 0){
            $this->goHome();
        }else{
            if(is_array($eventInfo)){
                $secRow = $this->event->seckill($eventInfo['num'],$eId);
                if($secRow == -1){
                    jsAlert('抢购失败,再试一次!');
                }elseif($secRow == 0){
                    jsAlert("已经抢光了");
                }elseif($secRow == 1){
                    jsAlert('恭喜您,抢到了!');
                }
            }
        }
        $this->goHome();
    }
}
