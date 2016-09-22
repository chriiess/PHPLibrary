<?php

class IdCard {

    // 根据身份证号，自动返回对应的星座
    public function get_xingzuo($cid) {

        if (!$this->isIdCard($cid)) {
            return;
        }

        $bir = substr($cid, 10, 4);
        $month = (int) substr($bir, 0, 2);
        $day = (int) substr($bir, 2);
        $strValue = '';
        if (($month == 1 && $day >= 20) || ($month == 2 && $day <= 18)) {
            $strValue = "水瓶座";
        } else if (($month == 2 && $day >= 19) || ($month == 3 && $day <= 20)) {
            $strValue = "双鱼座";
        } else if (($month == 3 && $day > 20) || ($month == 4 && $day <= 19)) {
            $strValue = "白羊座";
        } else if (($month == 4 && $day >= 20) || ($month == 5 && $day <= 20)) {
            $strValue = "金牛座";
        } else if (($month == 5 && $day >= 21) || ($month == 6 && $day <= 21)) {
            $strValue = "双子座";
        } else if (($month == 6 && $day > 21) || ($month == 7 && $day <= 22)) {
            $strValue = "巨蟹座";
        } else if (($month == 7 && $day > 22) || ($month == 8 && $day <= 22)) {
            $strValue = "狮子座";
        } else if (($month == 8 && $day >= 23) || ($month == 9 && $day <= 22)) {
            $strValue = "处女座";
        } else if (($month == 9 && $day >= 23) || ($month == 10 && $day <= 23)) {
            $strValue = "天秤座";
        } else if (($month == 10 && $day > 23) || ($month == 11 && $day <= 22)) {
            $strValue = "天蝎座";
        } else if (($month == 11 && $day > 22) || ($month == 12 && $day <= 21)) {
            $strValue = "射手座";
        } else if (($month == 12 && $day > 21) || ($month == 1 && $day <= 19)) {
            $strValue = "魔羯座";
        }
        return $strValue;
    }

    // 根据身份证号，自动返回对应的生肖
    public function get_shengxiao($cid) {

        if (!$this->isIdCard($cid)) {
            return;
        }

        $start = 1901;
        $end = $end = (int) substr($cid, 6, 4);
        $x = ($start - $end) % 12;
        $value = "";
        if ($x == 1 || $x == -11) {$value = "鼠";}
        if ($x == 0) {$value = "牛";}
        if ($x == 11 || $x == -1) {$value = "虎";}
        if ($x == 10 || $x == -2) {$value = "兔";}
        if ($x == 9 || $x == -3) {$value = "龙";}
        if ($x == 8 || $x == -4) {$value = "蛇";}
        if ($x == 7 || $x == -5) {$value = "马";}
        if ($x == 6 || $x == -6) {$value = "羊";}
        if ($x == 5 || $x == -7) {$value = "猴";}
        if ($x == 4 || $x == -8) {$value = "鸡";}
        if ($x == 3 || $x == -9) {$value = "狗";}
        if ($x == 2 || $x == -10) {$value = "猪";}
        return $value;
    }

    //根据身份证号，自动返回性别
    public function get_xingbie($cid, $comm = 0) {

        if (!$this->isIdCard($cid)) {
            return;
        }

        $sexint = (int) substr($cid, 16, 1);
        if ($comm > 0) {
            return $sexint % 2 === 0 ? '女士' : '先生';
        } else {
            return $sexint % 2 === 0 ? '女' : '男';
        }
    }

    public function isIdCard($number) {

        // 转化为大写，如出现x
        $number = strtoupper($number);
        //加权因子
        $wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码串
        $ai = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        //按顺序循环处理前17位
        $sigma = 0;
        for ($i = 0; $i < 17; $i++) {
            //提取前17位的其中一位，并将变量类型转为实数
            $b = (int) $number{$i};

            //提取相应的加权因子
            $w = $wi[$i];

            //把从身份证号码中提取的一位数字和加权因子相乘，并累加
            $sigma += $b * $w;
        }
        //计算序号
        $snumber = $sigma % 11;

        //按照序号从校验码串中提取相应的字符。
        $check_number = $ai[$snumber];

        if ($number{17} == $check_number) {
            return true;
        } else {
            return false;
        }
    }
}