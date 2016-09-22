<?php

include_once 'weixin.class.php'; //引用刚定义的微信消息处理类
include_once 'functions.php';
define("TOKEN", "mmhelper");
define('DEBUG', false);

$weixin = new Weixin(TOKEN, DEBUG); //实例化
$weixin->getMsg();
$type = $weixin->msgtype; //消息类型
$username = $weixin->msg['FromUserName']; //哪个用户给你发的消息,这个$username是微信加密之后的，但是每个用户都是一一对应的

$help_txt = "试试输入下面关键词获取信息:\r1.报时\r2.每日一句\r3.抽签\r4.猜谜\r5.笑话\r6.糗事\r7.QQ吉凶(例:1234)\r8.md5加密(例:md5 admin)\r9.计算器(例:计算器1+3-2）\r10.空气质量(例:厦门空气质量)\r11.天气(例:厦门天气)\r12.身份证(例:445224...)\r13.ip查询(例:12.64.235.86)\r14.手机归属地(例:13838383838)\r15.汉字转拼音(例:拼音小豆)\r16.查快递(例:快递1106279322505)\r17.藏头诗(例:藏头诗我为秋香)\r18.翻译(例:翻译我爱你)\r19.备案号(例:备案号douqq.com)\r20.银行卡(例:银行卡622848...)\r21.人民币数字转大写(例:大写1542)\r22.人品(例:人品刘小虎)\r23.磁力链接(例:磁力变形金刚)\r24.周公解梦(例:梦见结婚)\r25.新华字典(例:字典豆)\r26.汉语词典(例:词典小豆)\r27.成语词典(例:成语大智若愚)\r28.无损音乐(例:无损小苹果)\r29.查歌词(例:歌词小苹果)\r30.疾病症状(例:感冒症状)\r31.疾病病因(例:感冒病因)\r32.疾病治疗(例:感冒怎么治疗)\r33.菜谱查询(例:豆沙包的做法)\r34.百科查询(例:什么是机器人)\r35.历史上的今天\r36.百家姓(例:黄)\r37.知道问答(例:引起头晕的原因 或者 为什么我的电脑很卡)\r38.电影下载(例:电影疯狂动物城)\r39.短网址转换(例:短网址http://www.baidu.com)\r40.脑筋急转弯(例:脑筋急转弯)\r41.不在👆则自动聊天，谢谢关注，爱你么么哒😘";

if ($type === 'text') {
    if ($weixin->msg['Content'] == 'Hello2BizUser') {
        //微信用户第一次关注你的账号的时候，你的公众账号就会受到一条内容为'Hello2BizUser'的消息
        $reply = $weixin->makeText('欢迎你关注我,发送 “h” 获取帮助信息！');

    } elseif ($weixin->msg['Content'] == 'h' || $weixin->msg['Content'] == 'H' || $weixin->msg['Content'] == '帮助') {

        $reply = $weixin->makeText($help_txt);

    } elseif ($weixin->msg['Content'] == '黄泽耿' || $weixin->msg['Content'] == '泽耿') {

        $reply = $weixin->makeText($weixin->msg['Content'] . '是宇宙无敌第一大帅哥啦啦啦😝');

    } else {

        $content = Curl_get("http://api.douqq.com/?key=UjZDaHVjPVZQMDNwTmt4PUFuOUlMc3lzQlFvQUFBPT0&msg=" . $weixin->msg['Content']);
        $content = ClearHtml($content);
        $content = strCut($content, 1600, '...');
        $reply = $weixin->makeText($content);
    }
} elseif ($type === 'event') {

    //关注回复帮助文字
    if ($weixin->msg['Event'] == 'subscribe') {
        $reply = $weixin->makeText('欢迎你关注我,发送 “h” 获取帮助信息！');
    }

} elseif ($type === 'location') {
    //用户发送的是位置信息  稍后的文章中会处理
} elseif ($type === 'image') {
    //用户发送的是图片 稍后的文章中会处理
} elseif ($type === 'voice') {
    //用户发送的是声音 稍后的文章中会处理
}
$weixin->reply($reply);