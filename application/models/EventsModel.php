<?php
/**
 * @name EventsModel
 * @desc 抢购场次模型
 * @author root
 */
class EventsModel extends Model{

    private $redis = null;
    function __construct(){
        $this->redis = Yaf_Registry::get('redis');

        $this->table = TB_PREFIX."events";
        parent::__construct();
    }

    public function addEvents($events){
        $row = $this->Insert($events);
        if($row !== false){
            $this->redis->hMset("events".$row,$events);//存储场次信息
            $this->redis->rpush('eventID',$row);//存储场次列表
        }
        return $row;
    }
    public function delEventsByID($id){
        $row = $this->DeleteByID($id);
        if($row !== false) {
            $this->redis->hdel("events".$id);
            $this->redis->lrem('eventID',$id);
        }
        return $row;
    }

    public function updateEventsByID($id,$data){
        $events = array('start_time'=>$data['start_time'],'end_time'=>$data['end_time'],'goods_id'=>$data['goods_id'],'num'=>$data['num']);
        $row = $this->UpdateByID($events,$id);
        if($row !== false){
            $this->redis->hmset("events".$id,$events);
        }
        return $row;
    }

    public function getEventsByID($id){
        $field = array('id','start_time','end_time','goods_id','num');
        return $this->SelectByID($field,$id);
    }

    public function getEventsList($condition=array()){
        $field = array('id','start_time','end_time','goods_id','num');
        return $this->Field($field)->Where($condition)->Select();
    }

    /**
     * @param $eid 场次编号
     * @param string $time 请求时间戳
     * @return mixed
     *          0   场次不存在
     *          -1  时间已过期
     *          -2  时间未开始
     *          $event array   场次信息
     */
    public function checkEventDate($eid,$time=''){
        $event = $this->redis->hGetAll("events".$eid);
        if(!$event){
            return 0;
        }
        $time = $time?$time:time();

        if($time >= $event['start_time'] && $time <= $event['end_time']){
            return $event;
        }elseif($time >= $event['end_time']){
            return -1;
        }elseif($time <= $event['start_time']){
            return -2;
        }
    }

    /**
     * @param $storage  本场次剩余库存
     * @param $eid  场次编号
     * @return int
     *          0   抢光了
     *         -1   抢购失败
     *          1   抢购成功
     */
    public function seckill($storage,$eid){
        $msg = 0;
        if($storage > 0){
            $this->redis->watch("events".$eid);
            $this->redis->multi();
            $this->redis->hset("events".$eid,"num",$storage-1);
            $row = $this->redis->exec();
            if($row){
                $msg = 1;
            }else{
                $msg = -1;
            }
        }
        return $msg;
    }
}
