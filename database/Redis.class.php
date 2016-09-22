<?php

/**
 *
 * Redis类 PHP封装的操作Redis类
 *
 */
class Redis {

    const Arrays = '*'; //RESP Arrays类型
    const Bulk = '$'; //RESP Bulk Strings 类型
    const Integer = ':'; //RESP 整型数据
    const Simple = '+'; //RESP Simple Strings类型
    const Errors = '-'; //RESP Errors 错误类型

    const crlf = "\r\n";

    private $handle;

    private $host;
    private $port;
    private $quiet_fail;
    private $timeout;
    private $commands = array();
    private $result = true; //默认执行结果是正确的
    private $setError_func = false; //是否使用自定义错误处理函数处理错误信息
    private $used_command = null;
    private $errinfo = ''; //错误信息

    private $connect_timeout = 3;

    public function __construct($host, $port, $slient_fail = false, $timeout = 60) {
        if ($host && $port) {
            $this->connect($host, $port, $slient_fail, $timeout);
        }
    }
    /**
     * 连接Redis函数
     *
     * @param string $host  主机地址
     * @param number $port  服务端口
     * @param string $quiet_fail   是否屏蔽连接异常信息
     * @param number $timeout  设置读取资源超时时间
     */
    private function connect($host = '127.0.0.1', $port = 6379, $quiet_fail = false, $timeout = 60) {
        $this->host = $host;
        $this->port = $port;
        $this->quiet_fail = $quiet_fail;
        $this->timeout = $timeout;
        $this->handle = fsockopen($host, $port, $errno, $errstr, $this->connect_timeout);
        if ($this->quiet_fail) {
            $this->handle = @fsockopen($host, $port, $errno, $errstr, $this->connect_timeout);
            if (!$this->handle) {
                $this->handle = false;
            }
        } else {
            $this->handle = fsockopen($host, $port, $errno, $errstr, $this->connect_timeout);
        }
        if (is_resource($this->handle)) {
            stream_set_timeout($this->handle, $this->timeout);
        }
    }

    /**
     * 重新连接服务器函数
     */
    public function reconnect() {
        $this->__destruct();
        $this->connect($this->host, $this->port, $this->quiet_fail, $this->timeout);
    }

    /**
     * 构造发送命令函数
     * @return Redis
     */
    public function command() {
        if (!$this->handle) {
            return $this;
        }

        $args = func_get_args();
        $cmdlen = count($args);
        $command = '*' . $cmdlen . self::crlf;
        foreach ($args as $v) {
            $command .= '$' . strlen($v) . self::crlf . $v . self::crlf;
        }
        $this->commands[] = $command;
        return $this;
    }

    /**
     * 执行命令函数
     *
     * @return int
     */
    public function exec() {
        $count = sizeof($this->commands);
        if ($count < 1) {
            return false;
        }
        if ($this->setError_func) {
            $this->used_command = str_replace(self::crlf, '\\r\\n', implode(';', $this->commands));
        }
        $command = implode(self::crlf, $this->commands) . self::crlf;
        fwrite($this->handle, $command);
        $this->commands = array();
        return $count;

    }

    /**
     * 得到结果函数
     * @return boolean
     */
    public function result() {
        $result = false;
        $char = fgetc($this->handle);
        switch ($char) {
        case self::Simple:
            $result = $this->Simple_result();
            break;
        case self::Bulk:
            $result = $this->Bulk_result();
            break;
        case self::Arrays:
            $result = $this->Arrays_result();
            break;
        case self::Errors:
            $result = $this->Errors_result();
            break;
        case self::Integer:
            $result = $this->Integer_result();
            break;

        }
        return $result;
    }

    /**
     * 处理Simple Strings 类型响应的数据
     *
     * @return string
     */
    private function Simple_result() {

        return trim(fgets($this->handle));

    }

    /**
     * 处理 Bulk Strings 类型的数据
     * @return boolean|unknown
     */
    private function Bulk_result() {

        $result = trim(fgets($this->handle));

        if ($result == -1) {
            $this->errinfo = 'Nothing Replied';
            return false;
        }

        $result = $this->read_bulk_result($result);

        return $result;

    }

    /**
     * 处理 Arrays 类型的数据
     * @return boolean|multitype:NULL
     */
    private function Arrays_result() {
        $size = trim(fgets($this->handle));
        if ($size === -1) {
            $this->errinfo = 'Nothing Replied';
            return false;
        }
        $result = array();
        for ($i = 0; $i < $size; $i++) {
            $r = trim(fgets($this->handle));
            if ($r === -1) {
                return false;
            }
            $result[] = $this->read_bulk_result($r);
        }
        return $result;

    }

    /**
     * 处理RESP Integer 类型数据
     *
     * @return string
     */
    private function Integer_result() {
        return intval(trim(fgets($this->handle)));
    }

    /**
     * 错误处理函数
     * @return boolean
     */
    private function Errors_result() {
        $this->result = false;
        $err = fgets($this->handle);
        if ($this->setError_func) {
            call_user_func($this->setError_func, $this->used_command . "Error Info:" . $err);
        }
        $this->errinfo = $err;
        return false;

    }
    private function read_bulk_result($r) {
        $result = null;
        $read = 0;
        $size = (strlen($r) > 1 && substr($r, 0, 1) == self::Bulk) ? substr($r, 1) : $r;
        while ($read < $size) {
            $readsize = ($size - $read) > 1024 ? 1024 : $size - $read;

            $result .= fread($this->handle, $readsize);
            $read += $readsize;
        }

        fgets($this->handle);

        return $result;
    }

    /**
     * 析构函数
     */
    public function __destruct() {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }
    /**
     * 设置错误处理函数
     * @param unknown $function
     */
    public function setError_func($function) {
        $this->setError_func = $function;
    }

    public function get_errinfo() {
        return $this->errinfo;
    }
}

$obj = new Redis('192.168.144.133', 6379);
$obj->command('get', 'mykey', 'hello')->exec();
var_dump($obj->result());
echo $obj->get_errinfo();