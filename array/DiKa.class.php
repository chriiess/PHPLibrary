<?php

$color = array('red', 'green');
$size = array(39, 40, 41);
$local = array('beijing', 'shanghai');

echo "<pre>";
print_r(combineDika($color, $size, $local));

/**
 * 所有数组的笛卡尔积
 *
 * @param unknown_type $data
 */
function combineDika() {
    $data = func_get_args();
    $cnt = count($data);
    $result = array();
    foreach ($data[0] as $item) {
        $result[] = array($item);
    }
    for ($i = 1; $i < $cnt; $i++) {
        $result = combineArray($result, $data[$i]);
    }
    return $result;
}

/**
 * 两个数组的笛卡尔积
 *
 * @param unknown_type $arr1
 * @param unknown_type $arr2
 */
function combineArray($arr1, $arr2) {
    $result = array();
    foreach ($arr1 as $item1) {
        foreach ($arr2 as $item2) {
            $temp = $item1;
            $temp[] = $item2;
            $result[] = $temp;
        }
    }
    return $result;
}

?>