<?php

// 时区
date_default_timezone_set('Asia/Shanghai');

return array(

    'TMPL_STRIP_SPACE' => true, // 是否去除模板文件里面的html空格与换行

    'LOAD_EXT_CONFIG' => 'db,user',

    'URL_MODEL' => '2',

    'URL_HTML_SUFFIX' => 'jesus',

    'DEFAULT_FILTER' => 'htmlspecialchars,trim,remove_xss',

    'SHOW_ERROR_MSG' => true,

    'LOG_RECORD' => true,

    'LOG_LEVEL' => 'EMERG,ALERT,CRIT,ERR',

    'URL_CASE_INSENSITIVE' => false,

    'URL_ROUTER_ON' => true,

    'URL_ROUTE_RULES' => array(),

    'VAR_PAGE' => 'p',

    'COOKIE_PREFIX' => 'jss_ifttt_',

    'ERROR_PAGE' => __ROOT__ . '/Index/error404',

    'DEFAULT_EVENT' => 'vps'
);