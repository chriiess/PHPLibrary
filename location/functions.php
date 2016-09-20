<?php

/**
 * #######################################
 *
 * 地理位置相关代码片段
 *
 * #######################################
 */

/**
 * 查找两个经纬度之间的距离
 *
 * @param  $latitude1   float  起始纬度
 * @param  $longitude1  float  起始经度
 * @param  $latitude2   float  目标纬度
 * @param  $longitude2  float  目标经度
 * @return array(miles=>英里,feet=>英尺,yards=>码,kilometers=>公里,meters=>米)
 * @example
 *
 *         $point1 = array('lat' => 40.770623, 'long' => -73.964367);
 *         $point2 = array('lat' => 40.758224, 'long' => -73.917404);
 *         $distance = getDistanceBetweenPointsNew($point1['lat'], $point1['long'], $point2['lat'], $point2['long']);
 *         foreach ($distance as $unit => $value) {
 *             echo $unit.': '.number_format($value,4);
 *         }
 *
 *         The example returns the following:
 *
 *         miles: 2.6025       //英里
 *         feet: 13,741.4350   //英尺
 *         yards: 4,580.4783   //码
 *         kilometers: 4.1884  //公里
 *         meters: 4,188.3894  //米
 *
 */
function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2) {
    $theta = $longitude1 - $longitude2;
    $miles = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
    $miles = acos($miles);
    $miles = rad2deg($miles);
    $miles = $miles * 60 * 1.1515;
    $feet = $miles * 5280;
    $yards = $feet / 3;
    $kilometers = $miles * 1.609344;
    $meters = $kilometers * 1000;
    return compact('miles', 'feet', 'yards', 'kilometers', 'meters');
}

/**
 * 根据腾讯IP分享计划的地址获取IP所在地，比较精确
 *
 * @param   $queryIP string  ip地址
 * @return  $loc string  地址信息
 * @example
 *
 *          echo getIPLocQQ("119.129.211.109");
 *
 *          The example returns the following:
 *
 *          中国广东省广州市 电信
 */
function getIPLocQQ($queryIP) {
    $url = 'http://ip.qq.com/cgi-bin/searchip?searchip1=' . $queryIP;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_ENCODING, 'gb2312');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回
    $result = curl_exec($ch);
    $result = mb_convert_encoding($result, "utf-8", "gb2312"); // 编码转换，否则乱码
    curl_close($ch);
    preg_match("@<span>(.*)</span></p>@iU", $result, $ipArray);
    $loc = $ipArray[1];
    return $loc;
}

/**
 * 根据新浪IP查询接口获取IP所在地
 *
 * @param   $queryIP string  ip地址
 * @return  $loc string  地址信息
 * @example
 *
 *          echo getIPLocSina("119.129.211.109");
 *
 *          The example returns the following:
 *
 *          中国广东省广州市 电信
 */
function getIPLocSina($queryIP) {
    $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=' . $queryIP;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_ENCODING, 'utf8');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回
    $location = curl_exec($ch);
    $location = json_decode($location);
    curl_close($ch);

    $loc = "";
    if ($location === FALSE) {
        return "";
    }

    if (empty($location->desc)) {
        $loc = $location->country . $location->province . $location->city . $location->district . $location->isp;
    } else {
        $loc = $location->desc;
    }
    return $loc;
}