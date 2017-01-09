<?php

//加密函数
function passport_encrypt($txt, $key) {
    srand((double) microtime() * 1000000);
    //生成随机数字并md5的key
    $encrypt_key = md5(rand(0, 32000));
    $ctr = 0;
    $tmp = '';
    for ($i = 0; $i < strlen($txt); $i++) {
        //将随机key长度与原文长度一样
        $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
        // 随机key + 随机key和密文按位或运算的字符串 隔一位插入随机key
        $tmp .= $encrypt_key[$ctr] . ($txt[$i] ^ $encrypt_key[$ctr++]);
    }
    //再次使用 $key 和 $tmp 再次按位或运算 并返回base64数据
    return base64_encode(passport_key($tmp, $key));
}

//解密函数
function passport_decrypt($txt, $key) {
    //先base64解码 再按位或解密
    $txt = passport_key(base64_decode($txt), $key);
    $tmp = '';
    for ($i = 0; $i < strlen($txt); $i++) {
        $md5 = $txt[$i];
        $tmp .= $txt[++$i] ^ $md5;
    }

    return $tmp;
}

//密钥运算
function passport_key($txt, $encrypt_key) {
    $encrypt_key = md5($encrypt_key);
    $ctr = 0;
    $tmp = '';
    for ($i = 0; $i < strlen($txt); $i++) {
        //按位或加密过程,同上
        $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
        $tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
    }

    return $tmp;
}

$str = 'blog.fity.cn';
$key = 'iloveyou';

$entxt = passport_encrypt($str, $key);
echo $entxt . '<br/>';

$detxt = passport_decrypt($entxt, $key);
echo $detxt;