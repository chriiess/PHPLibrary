<?php
/**
 * Created by http://www.kuaidi.com
 * User: kuaidi.com PHP team
 * Date: 2016-03-02
 * 物流信息查询接口SDK
 * QQ: 524654214(群)
 * Version 1.0
 */

class KuaiDi {

    private $_APPKEY = '';

    private $_APIURL = "http://highapi.kuaidi.com/openapi-querycountordernumber.html?";

    private $_show = 0;

    private $_muti = 0;

    private $_order = 'desc';

    /**
     * 您获得的快递网接口查询KEY。
     * @param string $key
     */
    public function KuaidiAPi($key) {
        $this->_APPKEY = $key;
    }

    /**
     * 设置数据返回类型。0: 返回 json 字符串; 1:返回 xml 对象
     * @param number $show
     */
    public function setShow($show = 0) {
        $this->_show = $show;
    }

    /**
     * 设置返回物流信息条目数, 0:返回多行完整的信息; 1:只返回一行信息
     * @param number $muti
     */
    public function setMuti($muti = 0) {
        $this->_muti = $muti;
    }

    /**
     * 设置返回物流信息排序。desc:按时间由新到旧排列; asc:按时间由旧到新排列
     * @param string $order
     */
    public function setOrder($order = 'desc') {
        $this->_order = $order;
    }

    /**
     * 查询物流信息，传入单号，
     * @param 物流单号 $nu
     * @param 公司简码 $com 要查询的快递公司代码,不支持中文,具体请参考快递公司代码文档。 不填默认根据单号自动匹配公司。注:单号匹配成功率高于 95%。
     * @throws Exception
     * @return array
     */
    public function query($nu, $com = '') {
        if (function_exists('curl_init') == 1) {

            $url = $this->_APIURL;

            $dataArr = array(
                'id' => $this->_APPKEY,
                'com' => $com,
                'nu' => $nu,
                'show' => $this->_show,
                'muti' => $this->_muti,
                'order' => $this->_order,
            );

            foreach ($dataArr as $key => $value) {
                $url .= $key . '=' . $value . "&";
            }

            // echo $url;

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            $kuaidresult = curl_exec($curl);
            curl_close($curl);

            if ($this->_show == 0) {
                $result = json_decode($kuaidresult, true);
            } else {
                $result = $kuaidresult;
            }

            return $result;

        } else {
            throw new Exception("Please install curl plugin", 1);
        }
    }

}

//修改成你自己的KEY
$key = 'c684ab43a28bc3caea53570666ce9762';

$kuaidichaxun = new KuaiDi($key);

//设置返回格式。 0: 返回 json 字符串; 1:返回 xml 对象
//$kuaidichaxun->setShow(1); //可选，默认为 0 返回json格式

//返回物流信息条目数。 0:返回多行完整的信息; 1:只返回一行信息
//$kuaidichaxun->setMuti(1); //可选，默认为0

//设置返回物流信息排序。desc:按时间由新到旧排列; asc:按时间由旧到新排列
//$kuaidichaxun->setOrder('asc');

//查询
$result = $kuaidichaxun->query('407799180410', 'zhongtong');

//带公司短码查询，短码列表见文档
//$result = $kuaidichaxun->query('111111', 'quanfengkuaidi');

//111111 快递单号
//quanfengkuaidi   快递公司名称
echo "<pre>";
var_dump($result);

// 分类
// 快递公司代码
// 公司名称
// A

// aae
// AAE快递

// aramex
// Aramex快递

// auspost
// 澳大利亚邮政

// annengwuliu
// 安能物流快递
// B

// bht
// BHT快递

// baifudongfang
// 百福东方物流

// bangsongwuliu
// 邦送物流

// huitongkuaidi
// 百世汇通快递

// idada
// 百成大达物流
// C

// coe
// COE（东方快递）

// city100
// 城市100

// chuanxiwuliu
// 传喜物流
// D

// depx
// DPEX

// disifang
// 递四方

// dsukuaidi
// D速物流

// debangwuliu
// 德邦物流

// datianwuliu
// 大田物流

// dhl
// DHL国际快递

// dhlen
// DHL（国际件）

// dhlde
// DHL（德国件）

// dhlpoland
// DHL（波兰件）
// E

// ems
// EMS快递

// emsguoji
// EMS国际
// F

// fedex
// FedEx（国际）

// fedexus
// FedEx（美国）

// rufengda
// 凡客如风达

// feikangda
// 飞康达物流

// feibaokuaidi
// 飞豹快递

// fardarww
// Fardar Worldwide

// fandaguoji
// 颿达国际

// lianbangkuaidi
// FedEx（中国件）

// fedexuk
// FedEx（英国件）
// G

// gangzhongnengda
// 港中能达物流

// youzhengguonei
// 挂号信

// gongsuda
// 共速达

// guotongkuaidi
// 国通快递

// gls
// GLS
// H

// tiandihuayu
// 华宇物流

// hengluwuliu
// 恒路物流

// huaxialongwuliu
// 华夏龙物流

// tiantian
// 海航天天

// hebeijianhua
// 河北建华
// J

// jiajiwuliu
// 佳吉物流

// jiayiwuliu
// 佳怡物流

// jiayunmeiwuliu
// 加运美快递

// jixianda
// 急先达物流

// jinguangsudikuaijian
// 京广速递快件

// jinyuekuaidi
// 晋越快递

// jialidatong
// 嘉里大通

// jietekuaidi
// 捷特快递

// jd
// 京东快递

// jindawuliu
// 金大物流
// K

// kuaijiesudi
// 快捷快递

// kangliwuliu
// 康力物流

// kuayue
// 跨越物流
// L

// lianhaowuliu
// 联昊通物流

// longbangwuliu
// 龙邦速递

// lianbangkuaidi
// 联邦快递

// lejiedi
// 乐捷递

// lijisong
// 立即送
// M

// minghangkuaidi
// 民航快递

// meiguokuaidi
// 美国快递

// menduimen
// 门对门

// mingliangwuliu
// 明亮物流
// N

// ganzhongnengda
// 能达速递
// O

// ocs
// OCS

// ontrac
// OnTrac
// P

// pingandatengfei
// 平安达腾飞

// peixingwuliu
// 陪行物流
// Q

// quanfengkuaidi
// 全峰快递

// quanyikuaidi
// 全一快递

// quanritongkuaidi
// 全日通快递

// quanchenkuaidi
// 全晨快递

// sevendays
// 7天连锁物流
// R

// rufengda
// 如风达快递
// S

// shentong
// 申通快递

// shunfeng
// 顺丰速运

// suer
// 速尔快递

// haihongwangsong
// 山东海红

// shenghuiwuliu
// 盛辉物流

// shengfengwuliu
// 盛丰物流

// shangda
// 上大物流

// santaisudi
// 三态速递

// saiaodi
// 赛澳递

// shenganwuliu
// 圣安物流

// sxhongmajia
// 山西红马甲

// suijiawuliu
// 穗佳物流

// syjiahuier
// 沈阳佳惠尔
// T

// tnt
// TNT快递

// tiantian
// 天天快递

// tiandihuayu
// 天地华宇

// tonghetianxia
// 通和天下

// tianzong
// 天纵物流

// tntuk
// TNT（英国件）
// U

// ups
// UPS国际快递

// youshuwuliu
// UC优速快递
// W

// wanxiangwuliu
// 万象物流

// weitepai
// 微特派

// wanjiawuliu
// 万家物流
// X

// xinbangwuliu
// 新邦物流

// xinfengwuliu
// 信丰物流

// neweggozzo
// 新蛋物流

// hkpost
// 香港邮政

// xianglongyuntong
// 祥龙运通物流

// xianchengliansudi
// 西安城联速递
// Y

// yuantong
// 圆通速递

// yunda
// 韵达快运

// yuntongkuaidi
// 运通快递

// youzhengguonei
// 邮政国内

// youzhengguoji
// 邮政国际

// yuanchengwuliu
// 远成物流

// yafengsudi
// 亚风速递

// youshuwuliu
// 优速快递

// yuananda
// 源安达快递

// yuanfeihangwuliu
// 原飞航物流

// yuefengwuliu
// 越丰物流
// Z

// zhongtong
// 中通快递

// zhaijisong
// 宅急送

// zhongtiewuliu
// 中铁快运

// ztky
// 中铁物流

// zhongyouwuliu
// 中邮物流

// zhongtianwanyun
// 中天万运

// zhengzhoujianhua
// 郑州建华

// zhimakaimen
// 芝麻开门
