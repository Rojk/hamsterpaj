<?php
//profiler.php
// usage: include att beginning of script, call __profiler__('get') at the end of the script

function __profiler__($cmd = false) {
        static $log, $last_time, $total;
        list($usec, $sec) = explode(" ", microtime());
        $now = (float) $usec + (float) $sec;
        if($cmd) {
                if($cmd == 'get') {
                        unregister_tick_function('__profile__');
                        foreach($log as $function => $time) {
                                if($function != '__profile__') {
                                        $by_function[$function] = round($time / $total * 100, 2);
                                }
                        }
                        arsort($by_function);
                        return $by_function;
                }
                else if($cmd == 'init') {
                        $last_time = $now;
                        return;
                }
        }
        $delta = $now - $last_time;
        $last_time = $now;
        $trace = debug_backtrace();
        $caller = $trace[1]['function'];
        @$log[$caller] += $delta;
        $total += $delta;
}

__profiler__('init');
register_tick_function('__profiler__');
declare(ticks=1);

?> 