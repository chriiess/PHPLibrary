<?php
class LCS
{

    public $str1;
    public $str2;
    public $c = array();
    /*返回串一和串二的最长公共子序列
     */
    public function getLCS($str1, $str2, $len1 = 0, $len2 = 0)
    {
        $this->str1 = $str1;
        $this->str2 = $str2;
        if ($len1 == 0) {
            $len1 = strlen($str1);
        }

        if ($len2 == 0) {
            $len2 = strlen($str2);
        }

        $this->initC($len1, $len2);
        return $this->printLCS($this->c, $len1 - 1, $len2 - 1);
    }
    /*返回两个串的相似度
     */
    public function getSimilar($str1, $str2)
    {
        $len1 = strlen($str1);
        $len2 = strlen($str2);
        $len = strlen($this->getLCS($str1, $str2, $len1, $len2));
        return $len * 2 / ($len1 + $len2);
    }
    public function initC($len1, $len2)
    {
        for ($i = 0; $i < $len1; $i++) {
            $this->c[$i][0] = 0;
        }

        for ($j = 0; $j < $len2; $j++) {
            $this->c[0][$j] = 0;
        }

        for ($i = 1; $i < $len1; $i++) {
            for ($j = 1; $j < $len2; $j++) {
                if ($this->str1[$i] == $this->str2[$j]) {
                    $this->c[$i][$j] = $this->c[$i - 1][$j - 1] + 1;
                } else if ($this->c[$i - 1][$j] >= $this->c[$i][$j - 1]) {
                    $this->c[$i][$j] = $this->c[$i - 1][$j];
                } else {
                    $this->c[$i][$j] = $this->c[$i][$j - 1];
                }
            }
        }
    }
    public function printLCS($c, $i, $j)
    {
        if ($i == 0 || $j == 0) {
            if ($this->str1[$i] == $this->str2[$j]) {
                return $this->str2[$j];
            } else {
                return "";
            }

        }
        if ($this->str1[$i] == $this->str2[$j]) {
            return $this->printLCS($this->c, $i - 1, $j - 1) . $this->str2[$j];
        } else if ($this->c[$i - 1][$j] >= $this->c[$i][$j - 1]) {
            return $this->printLCS($this->c, $i - 1, $j);
        } else {
            return $this->printLCS($this->c, $i, $j - 1);
        }
    }
}

$lcs = new LCS();
//返回最长公共子序列
//$lcs->getLCS("hello word", "hello china");
//返回相似度
$t1 = '余弦定理和新闻的分类似乎是两件八杆子打不着的事，但是它们确有紧密的联系。具体说，新闻的分类很大程度上依靠余弦定理。Google 的新闻是自动分类和整理的。所谓新闻的分类无非是要把相似的新闻放到一类中。计算机其实读不懂新闻，它只能快速计算。这就要求我们设计一个算法来算出任意两篇新闻的相似性。为了做到这一点，我们需要想办法用一组数字来描述一篇新闻。我们来看看怎样找一组数字，或者说一个向量来描述一篇新闻。回忆一下我们在“如何度量网页相关性”一文中介绍的TF/IDF 的概念。对于一篇新闻中的所有实词，我们可以计算出它们的单文本词汇频率/逆文本频率值（TF/IDF)。不难想象，和新闻主题有关的那些实词频率高，TF/IDF 值很大。我们按照这些实词在词汇表的位置对它们的 TF/IDF 值排序。比如，词汇表有六万四千个词，分别为';

$t2 = '新闻分类——“计算机的本质上只能做快速运算，为了让计算机能够“算”新闻”(而不是读新闻)，就要求我们先把文字的新闻变成一组可计算的数字，然后再设计一个算法来算出任何两篇新闻的相似性。“——具体做法就是算出新闻中每个词的TF-IDF值，然后按照词汇表排成一个向量，我们就可以对这个向量进行运算了，那么如何度量两个向量？——向量的夹角越小，那么我们就认为它们更相似，而长度因为字数的不同并没有太大的意义。——如何计算夹角，那就用到了余弦定理（公式略）。——如何建立新闻类别的特征向量，有两种方法，手工和自动生成。至于自动分类的方法，书本上有介绍，我这里就略过了。很巧妙，但是我的篇幅肯定是放不下的。除余弦定理之外，还可以用矩阵的方法对文本进行分类，但这种方法需要迭代很多次，对每个新闻都要两两计算，但是在数学上有一个十分巧妙的方法——奇异值分解(SVD)。奇异值分解，就是把上面这样的大矩阵，分解为三个小矩阵的相乘。这三个小矩阵都有其物理含义。这种方法能够快速处理超大规模的文本分类，但是结果略显粗陋，如果两种方法一前一后结合使用，既能节省时间，又提高了精确性。';

echo $lcs->getSimilar($t1, $t2);
