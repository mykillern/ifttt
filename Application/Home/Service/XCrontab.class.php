<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 2017/7/16
 * Time: 下午4:11
 */

namespace Home\Service;


class XCrontab {

    /**
     * @param $time
     * @param $str_cron
     * @return true|string
     */
    static public function check($time, $str_cron) {
        $format_time = self::format_timestamp($time);
        $format_cron = self::format_crontab($str_cron);
        if (!is_array($format_cron)) {
            return $format_cron;
        }
        return self::format_check($format_time, $format_cron);
    }

    /**
     * 使用格式化的数据检查某时间($format_time)是否符合某个corntab时间计划($format_cron)
     * @param array $format_time self::format_timestamp()格式化时间戳得到
     * @param array $format_cron self::format_crontab()格式化的时间计划
     * @return bool
     */
    static public function format_check(array $format_time, array $format_cron) {
        return (!$format_cron[0] || in_array($format_time[0], $format_cron[0]))
            && (!$format_cron[1] || in_array($format_time[1], $format_cron[1]))
            && (!$format_cron[2] || in_array($format_time[2], $format_cron[2]))
            && (!$format_cron[3] || in_array($format_time[3], $format_cron[3]))
            && (!$format_cron[4] || in_array($format_time[4], $format_cron[4]))
            ;
    }

    /**
     * 格式化时间戳，以便比较
     * @param int $time 时间戳
     * @return array
     */
    static public function format_timestamp($time) {
        return explode('-', date('i-G-j-n-w', $time));
    }

    static public function format_crontab($str_cron) {
        $str_cron = trim($str_cron);
        $reg = '#^((\*(/\d+)?|((\d+(-\d+)?)(?3)?)(,(?4))*))( (?2)){4}$#';
        if (!preg_match($reg, $str_cron)) {
            return '格式错误';
        }
        try{
            //分别解析分、时、日、月、周
            $arr_cron = array();
            $parts = explode(' ', $str_cron);
            $arr_cron[0] = self::parse_cron_part($parts[0], 0, 59);//分
            $arr_cron[1] = self::parse_cron_part($parts[1], 0, 59);//时
            $arr_cron[2] = self::parse_cron_part($parts[2], 1, 31);//日
            $arr_cron[3] = self::parse_cron_part($parts[3], 1, 12);//月
            $arr_cron[4] = self::parse_cron_part($parts[4], 0, 6);//周（0周日）
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $arr_cron;
    }

    static protected function parse_cron_part($part, $f_min, $f_max) {
        $list = array();

        //处理"," -- 列表
        if (false !== strpos($part, ',')) {
            $arr = explode(',', $part);
            foreach ($arr as $v) {
                $tmp  = self::parse_cron_part($v, $f_min, $f_max);
                $list = array_merge($list, $tmp);
            }
            return $list;
        }

        //处理"/" -- 间隔
        $tmp  = explode('/', $part);
        $part  = $tmp[0];
        $step = isset($tmp[1]) ? $tmp[1] : 1;

        //处理"-" -- 范围
        if (false !== strpos($part, '-')) {
            list($min, $max) = explode('-', $part);
            if ($min > $max) {
                throw new \Exception('使用"-"设置范围时，左不能大于右');
            }
        } elseif ('*' == $part) {
            $min = $f_min;
            $max = $f_max;
        } else {//数字
            $min = $max = $part;
        }

        //空数组表示可以任意值
        if ($min==$f_min && $max==$f_max && $step==1) {
            return $list;
        }

        //越界判断
        if ($min < $f_min || $max > $f_max) {
            throw new \Exception('数值越界。应该：分0-59，时0-59，日1-31，月1-12，周0-6');
        }

        return $max-$min>$step ? range($min, $max, $step) : array((int)$min);
    }
}
