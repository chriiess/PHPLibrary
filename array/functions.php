<?php

/**
 * #######################################
 *
 * 数组处理相关代码片段
 *
 * #######################################
 */

/**
 * 把返回的数据集转换成树形结构
 *
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @return array $tree 树形结构数组
 */
function listToTree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {

    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * stdClass 对象转换成数组
 * @param $d object 对象
 * @return array 数组
 */
function objectToArray($d) {
    if (is_object($d)) {
        // Gets the properties of the given object
        // with get_object_vars function
        $d = get_object_vars($d);
    }
    if (is_array($d)) {
        /*
         * Return array converted to object
         * Using __FUNCTION__ (Magic constant)
         * for recursive call
         */
        return array_map(__FUNCTION__, $d);
    } else {
        // Return array
        return $d;
    }
}

/**
 * 数组转换成 stdClass 对象
 * @param $d array 数组
 * @return object 对象
 */
function arrayToObject($d) {
    if (is_array($d)) {
        /*
         * Return array converted to object
         * Using __FUNCTION__ (Magic constant)
         * for recursive call
         */
        return (object) array_map(__FUNCTION__, $d);
    } else {
        // Return object
        return $d;
    }
}

/**
 * 重定义二维数组的键值
 * @example
 *   $items = [
 *       ['id' => 10, 'name' => 'xxx'],
 *       ['id' => 11, 'name' => 'xxxx'],
 *   ]
 *
 *   $newItems = Arr::getDictionary($itmes, 'id');
 *
 *   $newItems value like this:
 *   [
 *      '10' => ['id' => 10, 'name' => 'xxx'],
 *      '11' => ['id' => 11, 'name' => 'xxxx'],
 *   ]
 *
 * @param array $items 二维数组
 * @param int|string $key         $items子数组里面的键名
 * @return array
 */
function getDictionary(Array $items, $key) {
    $dictionary = [];
    foreach ($items as $value) {
        $dictionary[$value[$key]] = $value;
    }
    return $dictionary;
}