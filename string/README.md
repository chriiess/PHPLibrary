字符串处理类

返回要截取的字符串,中文等于两个长度,英文等于一个长度
可以自定义要截取的长度,截取位置,截取后自带后缀,默认不带
```
// 实例


require "string.class.php";

$str  = '要截取zh@ng中文';

echo  stringClass::msubstr($str,7);

//返回结果
要截取z

```

