<?php

/**
 * #######################################
 *
 * 数字处理相关代码片段
 *
 * #######################################
 */

/**
 * 阿拉伯数字转中文大写金额
 * @param $num number 数字
 * @param $mode bool
 * @param $sim bool
 * @example

echo (NumToCNMoney(2.55) . "<br>");
echo (NumToCNMoney(2.55, 1, 0) . "<br>");
echo (NumToCNMoney(7965) . "<br>");
echo (NumToCNMoney(7965, 1, 0) . "<br>");
echo (NumToCNMoney(155555555.68) . "<br>");
echo (NumToCNMoney(155555555.68, 1, 0) . "<br>");
echo (NumToCNMoney(0.8888888) . "<br>");
echo (NumToCNMoney(0.8888888, 1, 0) . "<br>");
echo (NumToCNMoney(99999999999) . "<br>");
echo (NumToCNMoney(99999999999, 1, 0) . "<br>");

//输出
二元五角五分
贰元伍角伍分
七千九百六十五元
柒仟玖佰陆拾伍元
一亿五千五百五十五万五千五百五十五元六角八分
壹億伍仟伍佰伍拾伍萬伍仟伍佰伍拾伍元陆角捌分
零元八角八分
零元捌角捌分
九百九十九亿九千九百九十九万九千九百九十九元
玖佰玖拾玖億玖仟玖佰玖拾玖萬玖仟玖佰玖拾玖元

 */
function NumToCNMoney($num, $mode = true, $sim = true)
{

    if (!is_numeric($num)) {
        return '含有非数字非小数点字符！';
    }

    $char = $sim ? array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九')
    : array('零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖');
    $unit = $sim ? array('', '十', '百', '千', '', '万', '亿', '兆')
    : array('', '拾', '佰', '仟', '', '萬', '億', '兆');
    $retval = $mode ? '元' : '点';
    //小数部分
    if (strpos($num, '.')) {
        list($num, $dec) = explode('.', $num);
        $dec = strval(round($dec, 2));
        if ($mode) {
            $retval .= "{$char[$dec['0']]}角{$char[$dec['1']]}分";
        } else {
            for ($i = 0, $c = strlen($dec); $i < $c; $i++) {
                $retval .= $char[$dec[$i]];
            }
        }
    }
    //整数部分
    $str = $mode ? strrev(intval($num)) : strrev($num);
    for ($i = 0, $c = strlen($str); $i < $c; $i++) {
        $out[$i] = $char[$str[$i]];
        if ($mode) {
            $out[$i] .= $str[$i] != '0' ? $unit[$i % 4] : '';
            if ($i > 1 and $str[$i] + $str[$i - 1] == 0) {
                $out[$i] = '';
            }
            if ($i % 4 == 0) {
                $out[$i] .= $unit[4 + floor($i / 4)];
            }
        }
    }
    $retval = join('', array_reverse($out)) . $retval;
    return $retval;
}
