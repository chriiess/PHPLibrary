数组处理类

返回多层树形结构
```
// 实例


require "array.class.php";

$array = array(
	array("id"=>1,"name"=>"aa","pid"=>"0"),
	array("id"=>2,"name"=>"bb","pid"=>"1"),
	array("id"=>3,"name"=>"cc","pid"=>"2"),
);

$list = arrayClass::list_to_tree($array);


echo '<pre>';
print_r($list);

//返回结果

Array
(
    [0] => Array
        (
            [id] => 1
            [name] => aa
            [pid] => 0
            [_child] => Array
                (
                    [0] => Array
                        (
                            [id] => 2
                            [name] => bb
                            [pid] => 1
                            [_child] => Array
                                (
                                    [0] => Array
                                        (
                                            [id] => 3
                                            [name] => cc
                                            [pid] => 2
                                        )

                                )

                        )

                )

        )

)
```

