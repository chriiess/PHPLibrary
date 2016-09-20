<?php
/**
 * 图片处理类
 *
 * @example
 *
 *         $imageClass = new cutImageClass();
 *         $image = '1.jpg';
 *         $imageClass->loadImage($image);
 *         $imageClass->thumb(1); //1:裁剪中间.2裁剪左上角.3按比例缩放.4.直接缩放
 *         $url = $imageClass->getImagePath();
 *         @demo cutImageDemo
 */
class cutImageClass {

    private $originWidth;
    private $originHeight;
    private $originImage;

    private $width = '400';
    private $height = '400';
    private $path;
    private $imageName;
    private $ext;
    private $isRandName = true;
    //错误号
    private $errorNum = 0;
    //错误报告消息
    private $errorMessage = "";

    public function loadImage($originImage) {
        // 判断图片文件是否存在
        if (!file_exists($originImage)) {
            $this->setError(1);
            return false;
        }
        $info = getimagesize($originImage);
        // 判断是否能读出正确的图片信息
        if ($info == false) {
            $this->setError(2);
            return false;
        }
        // 读取信息
        $this->originImage = $originImage;
        $this->originWidth = $info[0];
        $this->originHeight = $info[1];
        $tmp = explode('/', $info['mime']);
        $this->ext = end($tmp);
        return true;
    }

    /**
     * 设置画布的宽
     * @description
     * @Author      dong
     * @DateTime    2016-09-04T19:22:22+0800
     * @param       int                   $width [description]
     */
    public function setWidth($width) {
        $this->width = $width;
    }

    /**
     * 设置画布的高
     * @description
     * @Author      dong
     * @DateTime    2016-09-04T19:22:22+0800
     * @param       int                   $width [description]
     */
    public function setHeight($height) {
        $this->height = $height;
    }

    /**
     * 设置是否随机重命名文件
     * @description
     * @Author      dong
     * @DateTime    2016-09-03T16:26:48+0800
     * @param       bool                   true为随机,false为不随机
     */
    public function setIsRandName($isRandName) {
        $this->isRandName = $isRandName;
    }

    /**
     * 设置新路径
     * @description
     * @Author      dong
     * @DateTime    2016-09-03T16:01:01+0800
     * @param       [string]
     *              $path [图片存放路径]
     */
    public function setPath($path) {
        $this->path = $path;
    }

    public function getImagePath() {
        return $this->imageName;
    }

    /**
     * 缩略图方法
     * @description
     * @Author      dong
     * @DateTime    2016-09-04T19:23:29+0800
     * @param       int                   $type 1:裁剪中间.2裁剪左上角.3按比例缩放.4.直接缩放
     * @return      [type]                         [description]
     */
    public function thumb($type = '1') {
        if (!$this->originImage) {
            $this->setError(3);
            return false;
        }
        // 创建一个目标画布
        $im = imagecreatetruecolor($this->width, $this->height);
        // 创建原始画布
        $createfunc = 'imagecreatefrom' . $this->ext;
        if (!function_exists($createfunc)) {
            return false;
        }
        $reim = $createfunc($this->originImage);
        //从图片信息中获取宽高计算缩放比例
        $wpre = $this->width / $this->originWidth;
        $hpre = $this->height / $this->originHeight;
        // 真正的缩放率(按照大的来)
        $pre = $wpre > $hpre ? $wpre : $hpre;
        // 用于裁剪比较的临时图的宽和高
        $tw = $this->width / $pre;
        $th = $this->height / $pre;
        switch ($type) {
        case '1':
            // 裁剪的X
            $ox = ($this->originWidth - $tw) / 2;
            // 裁剪的Y
            $oy = ($this->originHeight - $th) / 2;
            $x = 0;
            $y = 0;
            break;
        case '2':
            // 裁剪的XY
            $y = $x = $ox = $oy = 0;
            break;
        case '3':
            // 这种比较特别，不进行裁剪，而是缩放，但是是按照比较缩放，所以会有黑边
            $pre = $wpre < $hpre ? $wpre : $hpre;
            //计算缩放后的长宽
            $tw = $this->originWidth * $pre;
            $th = $this->originHeight * $pre;
            //计算目标画布的位置
            $ox = $oy = 0;
            $x = ($this->width - $tw) / 2;
            $y = ($this->height - $th) / 2;
            $this->width = $tw;
            $this->height = $th;
            $tw = $this->originWidth;
            $th = $this->originHeight;
            break;
        case '4':
            // 直接缩放
            $y = $x = $ox = $oy = 0;
            $tw = $this->originWidth;
            $th = $this->originHeight;
            break;
        default:
            return false;
            break;
        }

        // 缩放             新布  原布  新X 新Y  旧X    旧Y 新W           新H            旧W  旧H
        imagecopyresampled($im, $reim, $x, $y, $ox, $oy, $this->width, $this->height, $tw, $th);
        //生成图片保存函数
        $createfunc = 'image' . $this->ext;
        //保存图片
        $this->setNewImaName();
        $createfunc($im, $this->imageName);
        imagedestroy($im);
        imagedestroy($reim);
        return true;
    }

    public function setError($errorNum) {
        $this->setOption('errorNum', $errorNum);
        $this->errorMessage = $this->getError();
    }

    /**
     * 设置类里面的单个属性
     * @description
     * @Author      dong
     * @DateTime    2016-09-04T14:51:48+0800
     * @param       string                   $key 属性名
     * @param       string                   $val 属性的值
     */
    private function setOption($key, $val) {
        $this->$key = $val;
    }

    /**
     * 检查是否有存放文件文件的目录，有指定目录但是没有该目录则创建该目录
     * @description
     * @Author      dong
     * @DateTime    2016-09-04T14:57:56+0800
     * @return      bool                   true为合法，false为非法
     */
    private function checkFilePath() {
        if (empty($this->path)) {
            $this->setOption('errorNum', -5);
            return false;
        }
        if (!file_exists($this->path) || !is_writable($this->path)) {
            if (!@mkdir($this->path, 0755)) {
                $this->setOption('errorNum', -4);
                return false;
            }
        }
        return true;
    }

    /**
     * 设置上传后文件的名称
     * @description
     * @Author      dong
     * @DateTime    2016-09-04T14:52:51+0800
     */
    private function setNewImaName() {
        if ($this->isRandName) {
            $this->setOption('imageName', $this->setRandName());
        } else {
            $this->setOption('imageName', $this->originImage);
        }
    }

    /**
     * 设置随机文件名字
     * @description
     * @Author      dong
     * @DateTime    2016-09-04T14:54:34+0800
     * @return      string                   文件的名字
     */
    private function setRandName() {
        $fileName = date('YmdHis') . "_" . rand(100, 999);
        return $fileName . '.' . $this->ext;
    }

    /**
     * 获取错误信息
     * @param  void   没有参数
     * @return string  错误信息
     */
    public function getErrorMsg() {
        return $this->errorMessage;
    }

    /**
     * 设置错误信息大于0的错误是HTML那边上传的错误，小于0的则是PHP这边的错误
     * @description
     * @Author      dong
     * @DateTime    2016-09-04T14:44:17+0800
     * @return      string                   错误信息
     */
    private function getError() {
        $str = "图片{$this->originPath}载入出错 : ";
        switch ($this->errorNum) {
        case 1:$str .= "图片不存在";
            break;
        case 2:$str .= "该文件不是图片";
            break;
        case 3:$str .= "请先载入图片";
            break;
        default:$str .= "未知错误";
        }
        return $str;
    }

}

?>