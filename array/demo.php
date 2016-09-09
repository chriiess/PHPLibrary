<?php



require "array.class.php";

$array = array(
	array("id"=>1,"name"=>"aa","pid"=>"0"),
	array("id"=>2,"name"=>"bb","pid"=>"1"),
	array("id"=>3,"name"=>"cc","pid"=>"2"),
);

$list = arrayClass::list_to_tree($array);


echo '<pre>';
print_r($list);

