<?php
/*
 * 文章内容分页类
 */
header("Content-type: text/html; charset=utf-8");
class cutpage
{
    private $pagestr; //被切分的内容
    private $pagearr; //被切分文字的数组格式
    private $sum_word; //总字数(UTF-8格式的中文字符也包括)
    private $sum_page; //总页数
    private $page_word; //一页多少字
    private $cut_tag; //自动分页符
    private $cut_custom; //手动分页符
    private $page; //当前切分的页数，第几页
    private $url;

    public function __construct($pagestr, $page_word = 6500)
    {
        $this->page_word = $page_word;
        $this->cut_tag = array("</table>", "</div>", "</p>", "<br/>", "”。", "。", ".", "！", "……", "？", ",");
        $this->cut_custom = "{nextpage}";
        if (intval(trim($_GET['page']))) {
            $tmp_page = intval(trim($_GET['page']));
        } else {
            $tmp_page = 0;
        }
        $this->page = $tmp_page > 1 ? $tmp_page : 1;
        $this->pagestr = $pagestr;
    }
    public function cutStr()
    {
        $str_len_word = strlen($this->pagestr);
        //获取使用strlen得到的字符总数
        $i = 0;
        if ($str_len_word <= $this->page_word) {
            //如果总字数小于一页显示字数
            $page_arr[$i] = $this->pagestr;
        } else {
            if (strpos($this->pagestr, $this->cut_custom)) {
                $page_arr = explode($this->cut_custom, $this->pagestr);
            } else {
                $str_first = substr($this->pagestr, 0, $this->page_word);
                //0-page_word个文字 cutStr为func.global中的函数
                foreach ($this->cut_tag as $v) {
                    $cut_start = strrpos($str_first, $v);
                    //逆向查找第一个分页符的位置
                    if ($cut_start) {
                        $page_arr[$i++] = substr($this->pagestr, 0, $cut_start) . $v;
                        $cut_start = $cut_start + strlen($v);
                        break;
                    }
                }
                if (($cut_start + $this->page_word) >= $str_len_word) {
                    //如果超过总字数
                    $page_arr[$i++] = substr($this->pagestr, $cut_start, $this->page_word);
                } else {
                    while (($cut_start + $this->page_word) < $str_len_word) {
                        foreach ($this->cut_tag as $v) {
                            $str_tmp = substr($this->pagestr, $cut_start, $this->page_word); //取第cut_start个字后的page_word个字符
                            $cut_tmp = strrpos($str_tmp, $v); //找出从第cut_start个字之后，page_word个字之间，逆向查找第一个分页符的位置
                            if ($cut_tmp) {
                                $page_arr[$i++] = substr($str_tmp, 0, $cut_tmp) . $v;
                                $cut_start = $cut_start + $cut_tmp + strlen($v);
                                break;
                            }
                        }
                    }
                    if (($cut_start + $this->page_word) > $str_len_word) {
                        $page_arr[$i++] = substr($this->pagestr, $cut_start, $this->page_word);
                    }
                }
            }
        }
        $this->sum_page = count($page_arr); //总页数
        $this->pagearr = $page_arr;
        return $page_arr;
    }

    // 显示上一条，下一条
    public function pagenav()
    {
        $this->setUrl();
        $str = '';
        for ($i = 1; $i <= $this->sum_page; $i++) {
            if ($i == $this->page) {
                $str .= "<a href='#' class='cur'>" . $i . "</a> ";
            } else {
                $str .= "<a href='" . $this->url . $i . "'>" . $i . "</a> ";
            }
        }
        return $str;
    }

    public function setUrl()
    {
        parse_str($_SERVER["QUERY_STRING"], $arr_url);
        unset($arr_url["page"]);
        if (empty($arr_url)) {
            $str = "page=";
        } else {
            $str = http_build_query($arr_url) . "&page=";
        }
        $this->url = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"] . "?" . $str;
    }
}
