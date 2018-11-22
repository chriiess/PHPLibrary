<?php

/**
 * #######################################
 *
 * 图片处理相关代码片段
 *
 * #######################################
 */

/**
 * GD库画国旗
 */
function drawNationalFlag()
{
    $ing = imagecreatetruecolor(700, 410);
    $red = imagecolorallocate($ing, 255, 0, 0);
    $yellw = imagecolorallocate($ing, 255, 255, 45);

    imagefill($ing, 0, 0, $red);

    $a = array(90, 30, 108, 73, 157, 73, 119, 102, 135, 152, 93, 123, 52, 152, 66, 102, 29, 74, 76, 73, 90, 30);
    imagefilledpolygon($ing, $a, 10, $yellw);
    $a1 = array(229, 25, 229, 43, 248, 48, 229, 55, 229, 74, 217, 60, 198, 66, 210, 50, 197, 34, 218, 39, 229, 25);
    imagefilledpolygon($ing, $a1, 10, $yellw);
    $a2 = array(227, 108, 227, 127, 245, 134, 228, 139, 227, 157, 215, 143, 196, 149, 208, 132, 196, 117, 215, 122, 227, 108);
    imagefilledpolygon($ing, $a2, 10, $yellw);
    $a3 = array(163, 184, 163, 204, 181, 211, 163, 216, 163, 234, 152, 220, 132, 225, 144, 209, 132, 193, 151, 199, 163, 184);
    imagefilledpolygon($ing, $a3, 10, $yellw);
    $a4 = array(65, 209, 65, 228, 84, 235, 65, 240, 65, 259, 54, 245, 33, 249, 46, 233, 34, 217, 53, 224, 68, 209);
    imagefilledpolygon($ing, $a4, 10, $yellw);

    header("Content-Type:image/jpeg");
    imagejpeg($ing);
    imagedestroy($ing);
}
