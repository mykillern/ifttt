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
use Think\Log;

class CrontabController extends Controller
{

    public function triggerOneTime(){
        IftttService::trigger(time(),IftttService::TYPE_ONE_TIME);
        Log::write('triggerOneTime finish');
    }

    public function triggerCron(){
        IftttService::trigger(time(),IftttService::TYPE_CRON);
        Log::write('triggerCron finish');
    }

    public function disable(){
        IftttService::disableOldMissions();
        Log::write('disable finish');
    }
}