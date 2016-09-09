<?php
error_reporting(7);

include '../upload.class.php';

$uploadClass = new uploadClass();

// $uploadClass->setPath();  设置上传目录，默认为当前目录下的uploads
// $uploadClass->setAllowType(); 设置允许上传的类型，数组,默认['jpg', 'gif', 'png']
// $uploadClass->setMaxSize();  设置允许上传的大小
// $uploadClass->setIsRandName(); 设置是否随机生成保存文件的名字，默认是，格式为date('YmdHis') . "_" . rand(100, 999);不设置则原名保存
$return = $uploadClass->upload('field');
if ($return) {
	$list = $uploadClass->getFileName();
	if (!is_array($list)) {
		$list = array(0=>$list);
	}
} else {
	print_r($uploadClass->getErrorMsg());
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>上传文件</title>
</head>
<body>
	<?php foreach ($list as $k => $v): ?>
		<p>图<?=$k+1?></p>
		<p><img src="<?='./uploads/'.$v?>" height="200" width="200"/></p>
	<?php endforeach ?>
</body>
</html>