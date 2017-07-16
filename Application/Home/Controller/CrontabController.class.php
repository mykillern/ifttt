<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 2017/7/16
 * Time: 下午4:40
 */

namespace Home\Controller;


use Home\Service\IftttService;
use Think\Controller;

class CrontabController extends Controller
{

    public function triggerOneTime(){
        IftttService::trigger(time(),IftttService::TYPE_ONE_TIME);
    }

    public function triggerCron(){
        IftttService::trigger(time(),IftttService::TYPE_CRON);
    }
}