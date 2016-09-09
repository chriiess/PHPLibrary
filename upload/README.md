上传文件类

可以设置文件上传的类型、大小、路径、是否随机生产文名，支持多文件上传
例子如下，具体可以看demo

```
// 实例化
$uploadClass = new uploadClass();
// 设置上传目录，默认为当前目录下的uploads$uploadClass->setAllowType(); 设置允许上传的类型，数组,默认['jpg', 'gif', 'png']
$uploadClass->setPath();  
// 设置允许上传的大小
$uploadClass->setMaxSize();  
// 设置是否随机生成保存文件的名字，默认是，格式为date('YmdHis')
$uploadClass->setIsRandName(); 
$uploadClass->upload('field');
```

