<?php

error_reporting(7);

include '../cutImage.class.php';

$imageClass = new cutImageClass();
$image = '1.jpg';
$imageClass->loadImage($image);
$imageClass->thumb(1);
$url1 = $imageClass->getImagePath();
$imageClass->thumb(2);
$url2 = $imageClass->getImagePath();
$imageClass->thumb(3);
$url3 = $imageClass->getImagePath();
$imageClass->thumb(4);
$url4 = $imageClass->getImagePath();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>图裁剪</title>
</head>
<body>
	<p><img src="<?=$image?>"  alt="" /></p>
	<p></p>
	<p>
		<img src="<?=$url1?>"  alt="" />
		<img src="<?=$url2?>"  alt="" />
		<img src="<?=$url3?>"  alt="" />
		<img src="<?=$url4?>"  alt="" />
	</p>
</body>
</html>