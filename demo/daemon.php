<?php
/**
 * ActiveMQ Receive Daemon
 */

date_default_timezone_set("PRC");
declare(ticks = 1);

$queues = require_once(__DIR__ . '/console/config/queue.php');

define('SKS_PATH', dirname(dirname(__DIR__)) . '/sks');
$params = array_merge(require_once(__DIR__ . '/common/config/params.php'), require_once(__DIR__ . '/common/config/params-local.php'));

define('URL', $params['activeMQ']['url']);
define('USER', $params['activeMQ']['user']);
define('PASSWORD', $params['activeMQ']['password']);

$str_queues = [];
foreach ($queues as $k => $v) {
    foreach ($v as $kk => $vv) {
        $str_queues[$k . "." . $kk] = $vv;
    }
}

$pids = [];

$i = 0;
foreach ($str_queues as $k => $v) {
    if (isset($v['worker_num'])) {
        $worker_num = intval($v['worker_num']);
    } else {
        $worker_num = 1;
    }

    for ($ii = 0; $ii < $worker_num; $ii++) {

        $pids[$i] = pcntl_fork();

        if ($pids[$i] == -1) {
            MqLog("fork error : {$i}");
            exit;
        } elseif ($pids[$i] == 0) {
            // create a sub process
            MqLog("{$i}: sub process created");

            MqLog("queue: {$k}");
            receiveMQ($k, $v['sleep_time']);

            exit;
        } else {
            // create a master process
            MqLog("{$i}: {$pids[$i]} process created");
        }
        $i++;
    }
}

foreach ($pids as $i => $pid) {
    if ($pid) {
        pcntl_waitpid($pid, $status);
    }
}

function receiveMQ($queue, $sleep_time = 1)
{

    if (empty($queue)) {
        MqLog("queue param cannot be empty");
        exit;
    }
    $stomp = conectMQ();
    $stomp->subscribe($queue);
    try {
        while (true) {

            if ($stomp->error()) {
                MqLog("MQError:" . $stomp->error());
                $stomp = conectMQ(true);
                $stomp->subscribe($queue);
            }

            if ($stomp->hasFrame()) {
                $frame = $stomp->readFrame();
                if ($frame != null) {

                    MqLog("Received: " . $frame->body);

                    $m     = explode(".", $queue);
                    $argv1 = strtolower($m[0]) . '/' . strtolower($m[1]);
                    $argv2 = $frame->body;

                    $result = exec("./yiimq $argv1 $argv2");

                    if ($result == 1) {
                        // 执行命令行方法成功
                        MqLog("Success: $argv1 $argv2");
                        $stomp->ack($frame);
                    } else {
                        // todo 将失败队列任务存放到另外的位置
                        MqLog("Fail: $argv1 $argv2");
                    }
                }
            }

            if ($sleep_time > 0) {
                sleep($sleep_time);
            }
        }
    } catch (StompException $e) {
        MqLog('Connection failed: ' . $e->getMessage());
        exit(0);
    }
}

function conectMQ($flag = false)
{

    static $stomp;

    if ($flag || !$stomp) {
        try {
            $stomp = new Stomp(URL, USER, PASSWORD);
            MqLog('Connection Success');
        } catch (StompException $e) {
            MqLog(('Connection failed: ' . $e->getMessage() . ' again connect waiting 30s ...'));
            sleep(30);
            conectMQ(true);
        }
    }

    return $stomp;
}

function MqLog($msg)
{

    $msg .= " - time now is " . date("Y-m-d H:i:s") . "\n";
    //echo $msg;
    file_put_contents('./mq.log', $msg, FILE_APPEND);
}
