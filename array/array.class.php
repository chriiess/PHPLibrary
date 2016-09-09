<?php

/**
 * 数组类助手
 */

class arrayClass
{



    /**
     * 把返回的数据集转换成树形结构
     * @author gwing
     * @since 2016-08-09
     * @param array $list 要转换的数据集
     * @param string $pid parent标记字段
     * @return array $tree 树形结构数组
     */
    public static function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {

        // 创建Tree
        $tree = array();
        if(is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId =  $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                }else{
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }

}
