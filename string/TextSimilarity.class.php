<?php

require "./pscws23/pscws2.class.php";
/*
 *   文本相似度（余弦定理）
 *
 *   Author:宋小北（@xiaobeicn）
 *
 *   参考：
 *   http://www.ruanyifeng.com/blog/2013/03/cosine_similarity.html
 *       http://my.oschina.net/BreathL/blog/42477
 *
 *   Use:
 *   $obj = new TextSimilarity ($text1, $text2);
 *   echo $obj->run();
 */
Class TextSimilarity {
    /**
     * [排除的词语]
     *
     * @var array
     */
    private $_excludeArr = array('的', '了', '和', '呢', '啊', '哦', '恩', '嗯', '吧');

    /**
     * [词语分布数组]
     *
     * @var array
     */
    private $_words = array();

    /**
     * [分词后的数组一]
     *
     * @var array
     */
    private $_segList1 = array();

    /**
     * [分词后的数组二]
     *
     * @var array
     */
    private $_segList2 = array();

    /**
     * [分词两段文字]
     *
     * @param [type] $text1 [description]
     * @param [type] $text2 [description]
     */
    public function __construct($text1, $text2) {
        $this->_segList1 = $this->segment($text1);
        $this->_segList2 = $this->segment($text2);
    }

    /**
     * [外部调用]
     *
     * @return [type] [description]
     */
    public function run() {
        $this->analyse();
        $rate = $this->handle();
        return $rate ? $rate : 'errors';
    }

    /**
     * [分析两段文字]
     */
    private function analyse() {
        //t1
        foreach ($this->_segList1 as $v) {
            if (!in_array($v, $this->_excludeArr)) {
                if (!array_key_exists($v, $this->_words)) {
                    $this->_words[$v] = array(1, 0);
                } else {
                    $this->_words[$v][0] += 1;
                }
            }
        }

        //t2
        foreach ($this->_segList2 as $v) {
            if (!in_array($v, $this->_excludeArr)) {
                if (!array_key_exists($v, $words)) {
                    $this->_words[$v] = array(0, 1);
                } else {
                    $this->_words[$v][1] += 1;
                }
            }
        }
    }

    /**
     * [处理相似度]
     *
     * @return [type] [description]
     */
    private function handle() {
        $sum = $sumT1 = $sumT2 = 0;
        foreach ($this->_words as $word) {
            $sum += $word[0] * $word[1];
            $sumT1 += pow($word[0], 2);
            $sumT2 += pow($word[1], 2);
        }

        $rate = $sum / (sqrt($sumT1 * $sumT2));
        return $rate;
    }

    /**
     * [分词  【http://www.xunsearch.com/scws/docs.php#pscws23】]
     *
     * @param [type] $text [description]
     *
     * @return [type] [description]
     *
     * @description 分词只是一个简单的例子，你可以使用任意的分词服务
     */
    private function segment($text) {
        $outText = array();
        //实例化
        $so = new PSCWS2();
        //字符集
        $so->set_charset('utf8');
        //处理
        $so->send_text($text);

        //便利出需要的数组
        while ($res = $so->get_result()) {
            foreach ($res as $v) {
                $outText[] = $v['word'];
            }
        }
        //关闭
        $so->close();

        return $outText;
    }

}

$t1 = '余弦定理和新闻的分类似乎是两件八杆子打不着的事，但是它们确有紧密的联系。具体说，新闻的分类很大程度上依靠余弦定理。Google 的新闻是自动分类和整理的。所谓新闻的分类无非是要把相似的新闻放到一类中。计算机其实读不懂新闻，它只能快速计算。这就要求我们设计一个算法来算出任意两篇新闻的相似性。为了做到这一点，我们需要想办法用一组数字来描述一篇新闻。我们来看看怎样找一组数字，或者说一个向量来描述一篇新闻。回忆一下我们在“如何度量网页相关性”一文中介绍的TF/IDF 的概念。对于一篇新闻中的所有实词，我们可以计算出它们的单文本词汇频率/逆文本频率值（TF/IDF)。不难想象，和新闻主题有关的那些实词频率高，TF/IDF 值很大。我们按照这些实词在词汇表的位置对它们的 TF/IDF 值排序。比如，词汇表有六万四千个词，分别为';

$t2 = '新闻分类——“计算机的本质上只能做快速运算，为了让计算机能够“算”新闻”(而不是读新闻)，就要求我们先把文字的新闻变成一组可计算的数字，然后再设计一个算法来算出任何两篇新闻的相似性。“——具体做法就是算出新闻中每个词的TF-IDF值，然后按照词汇表排成一个向量，我们就可以对这个向量进行运算了，那么如何度量两个向量？——向量的夹角越小，那么我们就认为它们更相似，而长度因为字数的不同并没有太大的意义。——如何计算夹角，那就用到了余弦定理（公式略）。——如何建立新闻类别的特征向量，有两种方法，手工和自动生成。至于自动分类的方法，书本上有介绍，我这里就略过了。很巧妙，但是我的篇幅肯定是放不下的。除余弦定理之外，还可以用矩阵的方法对文本进行分类，但这种方法需要迭代很多次，对每个新闻都要两两计算，但是在数学上有一个十分巧妙的方法——奇异值分解(SVD)。奇异值分解，就是把上面这样的大矩阵，分解为三个小矩阵的相乘。这三个小矩阵都有其物理含义。这种方法能够快速处理超大规模的文本分类，但是结果略显粗陋，如果两种方法一前一后结合使用，既能节省时间，又提高了精确性。';

$obj = new TextSimilarity($t1, $t2);
echo $obj->run();