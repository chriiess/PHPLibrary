<?php

/**
 * 字符串类助手
 */

class stringClass
{



    /**
     * 等宽截取字符串函数
     * @description
     *  一个中文字符代表两个长度,一个英文字符代表一个长度
     * @author gwing
     * @since 2016-09-02
     * @param string $str 要截取的字符串
     * @param int $length 要截取的长度
     * @param int $start 要截取的起始位置
     * @param string $suffix 自定义后缀
     * @return string $new_str 返回截取的字符串
     */

    public static function msubstr($str, $length, $start=0, $suffix="") {

        $new_str = "";
        $str_length = $length+$start;


        for($i = $start; $i < $str_length; $i++){

            if(ord(mb_substr($str, $i, 1, 'utf-8')) > 0xa0)
                $str_length-=1;

            $new_str.= mb_substr($str, $i, 1, 'utf-8');
        }

        return $new_str.$suffix;
    }

}
