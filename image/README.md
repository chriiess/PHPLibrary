图处理类

暂时只有缩放的功能
拥有四种类型的缩放
例子如下，具体可以看demo
```
// 实例化
$imageClass = new imageClass();
$images = '1.jpg';
1:裁剪中间.2裁剪左上角.3按比例缩放.4.直接缩放
$imageClass->thumb(1);
$url1 = $imageClass->getImagePath();
$imageClass->thumb(2);
$url2 = $imageClass->getImagePath();
$imageClass->thumb(3);
$url3 = $imageClass->getImagePath();
$imageClass->thumb(4);
$url4 = $imageClass->getImagePath();
```

