<?php
namespace Home\Controller;
use Home\Service\IftttService;
use Think\Controller;

class IndexController extends Controller {

    public function index(){
        $all = IftttService::getAll();
        $this->assign('missions',$all);
        $this->display();
    }

    public function update(){
        $keys = array('key','type','time','cron','event','value1','value2','value3','status');
        $content_keys = array('value1','value2','value3');
        $params = $_REQUEST;
        $params = array_intersect_key($params,array_fill_keys($keys,0));
        $content = array_intersect_key($params,array_fill_keys($content_keys,0));
        if (!empty($params['time'])) $params['time'] = strtotime($params['time']);
        $result = IftttService::update($params['key'],$params['type'],$params['status'],$params['time'],$params['cron'],json_encode($content),$params['event']);
        $this->ajaxReturn(compact('result'));
    }
}