<?php

/**
 * curl类源码
 *
$ch = new Curl();
$ch->set_action("login", $loginurl, $refer);
$postdata = array("username" => "fortest", "password" => "12345");
$ch->open()->get_cookie($this->_cookie)->get("login", $postdata);
$result = $ch->header() . $ch->body();
$ch->close();
 */
class Curl {
    private $_is_temp_cookie = false;
    private $_header;
    private $_body;
    private $_ch;

    private $_proxy;
    private $_proxy_port;
    private $_proxy_type = 'HTTP'; // or SOCKS5
    private $_proxy_auth = 'BASIC'; // or NTLM
    private $_proxy_user;
    private $_proxy_pass;

    protected $_cookie;
    protected $_options;
    protected $_url = array();
    protected $_referer = array();

    public function __construct($options = array()) {
        $defaults = array();

        $defaults['timeout'] = 30;
        $defaults['temp_root'] = sys_get_temp_dir();
        $defaults['user_agent'] = 'Mozilla/5.0 (Windows; U; Windows NT 6.0; zh-CN; rv:1.8.1.20) Gecko/20081217 Firefox/2.0.0.20';

        $this->_options = array_merge($defaults, $options);
    }

    public function open() {
        $this->_ch = curl_init();

        //curl_setopt ( $this->_ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt($this->_ch, CURLOPT_HEADER, true);
        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_ch, CURLOPT_USERAGENT, $this->_options['user_agent']);
        curl_setopt($this->_ch, CURLOPT_CONNECTTIMEOUT, $this->_options['timeout']);
        curl_setopt($this->_ch, CURLOPT_HTTPHEADER, array('Expect:')); // for lighttpd 417 Expectation Failed

        $this->_header = '';
        $this->_body = '';

        return $this;
    }

    public function close() {
        if (is_resource($this->_ch)) {
            curl_close($this->_ch);
        }

        if (isset($this->_cookie) && $this->_is_temp_cookie && is_file($this->_cookie)) {
            unlink($this->_cookie);
        }
    }

    public function cookie() {
        if (!isset($this->_cookie)) {
            if (!empty($this->_cookie) && $this->_is_temp_cookie && is_file($this->_cookie)) {
                unlink($this->_cookie);
            }

            $this->_cookie = tempnam($this->_options['temp_root'], 'curl_manager_cookie_');
            $this->_is_temp_cookie = true;
        }

        curl_setopt($this->_ch, CURLOPT_COOKIEJAR, $this->_cookie);
        curl_setopt($this->_ch, CURLOPT_COOKIEFILE, $this->_cookie);

        return $this;
    }

    public function get_cookie($cookfile) {

        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_ch, CURLOPT_COOKIEJAR, $cookfile);

        return $this;
    }

    public function set_cookie($cookfile) {

        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_ch, CURLOPT_COOKIEFILE, $cookfile);

        return $this;
    }

    public function set_session($session) {

        curl_setopt($this->_ch, CURLOPT_COOKIESESSION, $session);

        return $this;
    }

    public function ssl() {
        curl_setopt($this->_ch, CURLOPT_SSL_VERIFYPEER, false);

        return $this;
    }

    public function proxy($host = null, $port = null, $type = null, $user = null, $pass = null, $auth = null) {
        $this->_proxy = isset($host) ? $host : $this->_proxy;
        $this->_proxy_port = isset($port) ? $port : $this->_proxy_port;
        $this->_proxy_type = isset($type) ? $type : $this->_proxy_type;

        $this->_proxy_auth = isset($auth) ? $auth : $this->_proxy_auth;
        $this->_proxy_user = isset($user) ? $user : $this->_proxy_user;
        $this->_proxy_pass = isset($pass) ? $pass : $this->_proxy_pass;

        if (!empty($this->_proxy)) {
            curl_setopt($this->_ch, CURLOPT_PROXYTYPE, $this->_proxy_type == 'HTTP' ? CURLPROXY_HTTP : CURLPROXY_SOCKS5);
            curl_setopt($this->_ch, CURLOPT_PROXY, $this->_proxy);
            curl_setopt($this->_ch, CURLOPT_PROXYPORT, $this->_proxy_port);
        }

        if (!empty($this->_proxy_user)) {
            curl_setopt($this->_ch, CURLOPT_PROXYAUTH, $this->_proxy_auth == 'BASIC' ? CURLAUTH_BASIC : CURLAUTH_NTLM);
            curl_setopt($this->_ch, CURLOPT_PROXYUSERPWD, "[{$this->_proxy_user}]:[{$this->_proxy_pass}]");
        }

        return $this;
    }

    public function post($action, $query = array()) {
        if (is_array($query)) {
            foreach ($query as $key => $val) {
                if (!empty($val) && $val{0} != '@') {
                    $encode_key = urlencode($key);

                    if ($encode_key != $key) {
                        unset($query[$key]);
                    }

                    //$query[$encode_key] = urlencode($val);
                    $query[$encode_key] = $val;
                }
            }
        }

        curl_setopt($this->_ch, CURLOPT_POST, true);
        curl_setopt($this->_ch, CURLOPT_URL, $this->_url[$action]);
        curl_setopt($this->_ch, CURLOPT_REFERER, $this->_referer[$action]);
        curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $query);

        $this->_requrest();

        return $this;
    }

    public function get($action, $query = array()) {
        $url = $this->_url[$action];

        if (!empty($query)) {
            $url .= strpos($url, '?') === false ? '?' : '&';
            $url .= is_array($query) ? http_build_query($query) : $query;
        }

        curl_setopt($this->_ch, CURLOPT_URL, $url);
        curl_setopt($this->_ch, CURLOPT_REFERER, $this->_referer[$action]);

        $this->_requrest();

        return $this;
    }

    public function getinfo() {
        return curl_getinfo($this->_ch);
    }
    public function output_trace($output) {
        curl_setopt($this->_ch, CURLOPT_VERBOSE, 1);
        curl_setopt($this->_ch, CURLOPT_STDERR, $output);

        return $this;
    }

    public function download($action, $query = array(), $saveto, $rewritemode = 0) {
        $url = $this->_url[$action];
        if (!empty($query)) {
            $url .= strpos($url, '?') === false ? '?' : '&';
            $url .= is_array($query) ? http_build_query($query) : $query;
        }
        try
        {
            if ($rewritemode == 1 || !file_exists($saveto)) {
                $fp = @fopen($saveto, "wb");
                @curl_setopt($this->_ch, CURLOPT_FILE, $fp);
                curl_setopt($this->_ch, CURLOPT_URL, $url);
                curl_setopt($this->_ch, CURLOPT_HEADER, 0);
                curl_setopt($this->_ch, CURLOPT_REFERER, $this->_referer[$action]);
                curl_exec($this->_ch);

                curl_close($this->_ch);

                @fclose($fp);
            }
            return true;
        } catch (exception $e) {
            return false;
        }
    }

    public function put($action, $query = array()) {
        curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, 'PUT');

        return $this->post($action, $query);
    }

    public function delete($action, $query = array()) {
        curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        return $this->post($action, $query);
    }

    public function head($action, $query = array()) {
        curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, 'HEAD');

        return $this->post($action, $query);
    }

    public function options($action, $query = array()) {
        curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, 'OPTIONS');

        return $this->post($action, $query);
    }

    public function trace($action, $query = array()) {
        curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, 'TRACE');

        return $this->post($action, $query);
    }

    public function connect() {

    }

    public function follow_location() {
        preg_match('#Location:\s*(.+)#i', $this->header(), $match);

        if (isset($match[1])) {
            $this->set_action('auto_location_gateway', $match[1], $this->effective_url());

            $this->get('auto_location_gateway')->follow_location();
        }

        return $this;
    }

    public function set_action($action, $url, $referer = '') {
        $this->_url[$action] = $url;
        $this->_referer[$action] = $referer;

        return $this;
    }

    public function header() {
        return $this->_header;
    }

    public function body() {
        return $this->_body;
    }

    public function effective_url() {
        return curl_getinfo($this->_ch, CURLINFO_EFFECTIVE_URL);
    }

    public function http_code() {
        return curl_getinfo($this->_ch, CURLINFO_HTTP_CODE);
    }

    private function _requrest() {
        $response = curl_exec($this->_ch);

        $errno = curl_errno($this->_ch);

        if ($errno > 0) {
            throw new Curl_Manager_Exception(curl_error($this->_ch), $errno);
        }

        $header_size = curl_getinfo($this->_ch, CURLINFO_HEADER_SIZE);

        $this->_header = substr($response, 0, $header_size);
        $this->_body = substr($response, $header_size);
    }

    public function __destruct() {
        $this->close();
    }
}