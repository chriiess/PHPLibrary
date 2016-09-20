<?php

/**
 * #######################################
 *
 * 文件处理相关代码片段
 *
 * #######################################
 */

/**
 * 在一个目录中列出所有文件和文件夹
 */
function list_files($dir) {
    if (is_dir($dir)) {
        if ($handle = opendir($dir)) {
            while (($file = readdir($handle)) !== false) {
                if ($file != "." && $file != "..") {
                    echo '<a target="_blank" href="' . $dir . $file . '">' . $file . '</a><br>' . "\n";
                }
            }
            closedir($handle);
        }
    }
}