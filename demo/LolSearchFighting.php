<?php
header("content-type:text/html; charset=utf-8");

//jiequstr函数定义开始
function jiequstr($mubiaostr, $ksstr, $jsstr) {
    //$mubiaostr---------目标字符串
    //$ksstr---------截取开始字符串，支持通配符(*)
    //$jsstr---------截取结束字符串，支持通配符(*)
    if ($mubiaostr == '') {
        echo '目标字符串为空<br/>';
        return false;
    }
    if ($ksstr == '') {
        $jiequks = 0;
        return false;
    } else {
        $chucuo1 = 0;
        $arr1 = explode('(*)', $ksstr);
        $len1 = count($arr1);
        $chaxunwz = 0;
        $feikongnum1 = 0;
        for ($i = 0; $i < $len1; $i++) {
            if ($arr1[$i] == '') {
                continue;
            }
            $feikongnum1++;
            if (($wz = strpos($mubiaostr, $arr1[$i], $chaxunwz)) !== false) {
                $chaxunwz = $wz + strlen($arr1[$i]);
            } else {
                $chucuo1 = 1;
                return false;
                break;
            }
        }
        if ($chucuo1 == 1) {
            $jiequks = 0;
        } else {
            $jiequks = $chaxunwz;
        }
    }
    if ($jsstr == '') {
        $jiequjs = strlen($mubiaostr);
        return false;
    } else {
        $chucuo2 = 0;
        $arr2 = explode('(*)', $jsstr);
        $len2 = count($arr2);
        $chaxunwz = $jiequks;
        $feikongnum2 = 0;
        for ($i = 0; $i < $len2; $i++) {
            if ($arr2[$i] == '') {
                continue;
            }
            $feikongnum2++;
            if (($wz = strpos($mubiaostr, $arr2[$i], $chaxunwz)) !== false) {
                $chaxunwz = $wz + strlen($arr2[$i]);
                if ($feikongnum2 == 1) {
                    $enddian = $wz;
                }
            } else {
                $chucuo2 = 1;
                return false;
                break;
            }
        }
        if ($chucuo2 == 1) {
            $jiequjs = strlen($mubiaostr);
        } else {
            $jiequjs = $enddian;
        }
    }
    $jiequstr = substr($mubiaostr, $jiequks, $jiequjs - $jiequks);
    return $jiequstr;
}

function lol_zhanloule_serach($nichen, $qu) {
    $lol = array($qu, $nichen);

    print_r($lol);

    switch ($lol[1]) {
    case "教育网专区":
        $lol[1] = "教育";
        break;
    case "艾欧尼亚":
        $lol[1] = "电信一";
        break;
    case "祖安":
        $lol[1] = "电信二";
        break;
    case "诺卡萨斯":
        $lol[1] = "电信三";
        break;
    case "班德尔城":
        $lol[1] = "电信四";
        break;
    case "皮尔特沃夫":
        $lol[1] = "电信五";
        break;
    case "战争学院":
        $lol[1] = "电信六";
        break;
    case "巨神峰":
        $lol[1] = "电信七";
        break;
    case "雷瑟守备":
        $lol[1] = "电信八";
        break;
    case "裁决之地":
        $lol[1] = "电信九";
        break;
    case "黑色玫瑰":
        $lol[1] = "电信十";
        break;
    case "暗影岛":
        $lol[1] = "电信十一";
        break;
    case "钢铁烈阳":
        $lol[1] = "电信十二";
        break;
    case "均衡教派":
        $lol[1] = "电信十三";
        break;
    case "水晶之痕":
        $lol[1] = "电信十四";
        break;
    case "影流":
        $lol[1] = "电信十五";
        break;
    case "守望之海":
        $lol[1] = "电信十六";
        break;
    case "征服之海":
        $lol[1] = "电信十七";
        break;
    case "卡拉曼达":
        $lol[1] = "电信十八";
        break;
    case "皮城警备":
        $lol[1] = "电信十九";
        break;
    case "比尔吉沃特":
        $lol[1] = "网通一";
        break;
    case "德玛西亚":
        $lol[1] = "网通二";
        break;
    case "费雷尔卓得":
        $lol[1] = "网通三";
        break;
    case "无畏先锋":
        $lol[1] = "网通四";
        break;
    case "怒瑞玛":
        $lol[1] = "网通五";
        break;
    case "扭曲丛林":
        $lol[1] = "网通六";
        break;
    }
    $url = "http://lolbox.duowan.com/playerDetail.php?serverName=" . $lol[1] . "&playerName=" . $lol[0];
    $shuchu = file_get_contents($url);

    $q = '算法</a>'; //截取前字符串
    $h = '</p>';
    $jintian = jiequstr($shuchu, $q, $h);
    $jintian = str_replace("\r", "", $jintian);
    $jintian = str_replace("\t", "", $jintian);
    $jintian = str_replace("\n", "", $jintian);
    $jintian = str_replace("\r\n", "", $jintian);
    $jintian = str_replace(" ", "", $jintian);

    return $lol[0] . "(" . $lol[1] . ")" . "的战斗力是：\n" . $jintian;
}

echo lol_zhanloule_serach("水晶之痕", "swoff");

?>
