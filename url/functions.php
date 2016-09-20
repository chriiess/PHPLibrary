<?php

/**
 * 获取来源url
 */
function getReferer() {
    return str_replace(array(SINA_LOGIN_URL, TX_LOGIN_URL), '', $_SERVER['HTTP_REFERER']);
}

/**
 * 获取url后缀
 */
function getUrlSuffix($filename = '') {
    return strrchr($filename, '.');
}
