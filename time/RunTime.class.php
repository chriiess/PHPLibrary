<?php

/**
 * 用来测试一个函数或一段代码的执行速度
 */
class RunTime {

    private $_startTime; //开始时间
    private $_endTime; //结束时间

    public function startTime() {
        list($usec, $sec) = explode(' ', microtime());
        $this->_startTime = $sec + $usec;
    }
    public function endTime() {
        list($usec, $sec) = explode(' ', microtime());
        $this->_endTime = $sec + $usec;
    }
    public function timeSpend() {
        if (isset($this->_startTime) && !is_null($this->_startTime) && isset($this->_endTime) && !is_null($this->_endTime)) {
            return "程序共执行：" . ($this->_endTime - $this->_startTime) . '秒';
        } else {
            return '请设置开始时间或结束时间';
        }
    }
}