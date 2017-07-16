<?php

namespace Home\Service;
use Iftttrigger\MakerTrigger;
use Think\Log;

/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 2017/7/16
 * Time: 下午2:42
 */
class IftttService
{

    const TYPE_ONE_TIME = 1;
    const TYPE_CRON = 2;

    const STATUS_ACTIVE = 1;
    const STATUS_NOUSE = 2;

    /**
     * @param bool $active
     * @return mixed
     */
    static public function getAll($active = true){
        $condition = array();
        if ($active) $condition['status'] = self::STATUS_ACTIVE;
        $r = M('Ifttt')->where($condition)->getField('key,event,type,time,content,cron,status');
        foreach ($r as &$info){
            $info['type_show'] = $info['type'] == self::TYPE_ONE_TIME ? 'one time' : 'cron';
            $info['status_show'] = $info['status'] == self::STATUS_ACTIVE ? 'active' : 'nouse';
            $info['time_show'] = $info['time'] > 0 ? date('Y-m-d H:i:s',$info['time']) : '';
        }
        return $r;
    }

    /**
     * one time missions
     * @param $from
     * @param $to
     * @return array
     */
    static public function getOneTimeMissions($from,$to){
        $condition = array(
            'status' => self::STATUS_ACTIVE,
            'type' => self::TYPE_ONE_TIME,
            'time' => array('between',array($from,$to))
        );
        $r = M('Ifttt')->where($condition)->getField('key,event,time,content');
        return $r ? : [];
    }

    /**
     * cron missions
     * @param $time
     * @return array
     */
    static public function getCronMissions($time){
        $condition = array(
            'status' => self::STATUS_ACTIVE,
            'type' => self::TYPE_CRON
        );
        $all = M('Ifttt')->where($condition)->getField('key,event,cron,content');
        $result = [];
        foreach ($all as $item){
            if (XCrontab::check($time,$item['cron']) === true){
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * trigger
     * @param $time
     * @param $type
     */
    static public function trigger($time,$type){
        $time = strtotime(date('Y-m-d H:i:00',$time));
        if ($type == self::TYPE_ONE_TIME){
            $from = $time;
            $to = $time + 59;
            $missions = self::getOneTimeMissions($from,$to);
        }else{
            $missions = self::getCronMissions($time);
        }
        if ($missions){
            $mt = new MakerTrigger(C('KEY_IFTTT'));
            $async = C('ASYNC');
            $async = $async == true;
            foreach ($missions as $m){
                $mt->fire($m['event'],json_decode($m['content'],true),$async);
                Log::write('trigger '.json_encode($m));
            }
        }
    }

    /**
     * @param $key
     * @param $type
     * @param $status
     * @param $time
     * @param $cron
     * @param $content
     * @param $event
     */
    static public function update($key,$type,$status = 0,$time = 0,$cron = null,$content = null,$event = null){
        $mdl = M('Ifttt');
        $old = $mdl->where(compact('key'))->find();
        if ($old['id'] > 0){
            if (!empty($status)) $old['status'] = $status;
            if (!empty($time)) $old['time'] = $time;
            if (!empty($cron)) $old['cron'] = $cron;
            if (!empty($content)) $old['content'] = $content;
            if (!empty($event)) $old['event'] = $event;
            $r = $mdl->save($old) !== false;
        }else{
            if (empty($status)) $status = self::STATUS_ACTIVE;
            if (empty($content)) $content = json_encode(array());
            if (empty($event)) $event = C('DEFAULT_EVENT');
            if (empty($cron)) $cron = '';
            $r = $mdl->add(compact('key','type','status','time','cron','content','event')) !== false;
        }
        return $r;
    }

    /**
     * disable
     */
    static public function disableOldMissions(){
        $to = strtotime('today');
        M('Ifttt')->where(array('type' => self::TYPE_ONE_TIME,'time' => array('lt',$to)))->setField('status',self::STATUS_NOUSE);
    }
}