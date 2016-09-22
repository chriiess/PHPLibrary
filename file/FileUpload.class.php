<?php

/**
 * Description of FileUpload
 *  文件上传类
 * @author albafica.wang
 * @createdate 2014/11/03
 */
class FileUpload {

    /**
     * 默认配置定义
     * @var type
     */
    private $_config = array(
        'maxSize' => -1, // 上传文件的最大值
        'supportMulti' => true, // 是否支持多文件上传
        'allowTypes' => array(), // 允许上传的文件类型 留空不做检查
        'autoSub' => false, // 启用子目录保存文件
        'subType' => 'hash', // 子目录创建方式 可以使用hash date custom
        'subDir' => '', // 子目录名称 subType为custom方式后有效
        'dateFormat' => 'Ymd',
        'hashLevel' => 1, // hash的目录层次
        'savePath' => '', // 上传文件保存路径
        'autoCheck' => true, // 是否自动检查附件
        'uploadReplace' => false, // 存在同名是否覆盖
        'saveRule' => 'uniqid', // 上传文件命名规则
        'hashType' => 'md5_file', // 上传文件Hash规则函数名
        //上传图片相关处理参数
        'thumb' => false, // 使用对上传图片进行缩略图处理
        'imageClassPath' => 'ORG.Util.Image', // 图库类包路径
        'thumbMaxWidth' => '', // 缩略图最大宽度
        'thumbMaxHeight' => '', // 缩略图最大高度
        'thumbPrefix' => 'thumb_', // 缩略图前缀
        'thumbSuffix' => '', //缩略图后缀
        'thumbPath' => '', // 缩略图保存路径
        'thumbFile' => '', // 缩略图文件名
        'thumbExt' => '', // 缩略图扩展名
        'thumbRemoveOrigin' => false, // 是否移除原图
        'thumbType' => 1, // 缩略图生成方式 1 按设置大小截取 0 按原图等比例缩略
        'zipImages' => false, // 压缩图片文件上传
    );

    /**
     * 文件mime值
     * @var type
     */
    private $_mimes = array(
        'csv' => array(
            'text/x-comma-separated-values',
            'text/comma-separated-values',
            'application/octet-stream',
            'application/vnd.ms-excel',
            'application/x-csv',
            'text/x-csv',
            'text/csv',
            'text/plain',
            'application/csv',
            'application/excel',
            'application/vnd.msexcel',
        ),
        'vcf' => array('text/x-vcard'),
        'xls' => array(
            'application/excel',
            'application/vnd.ms-excel',
            'application/msexcel',
            'application/msword',
        ),
        'ppt' => array(
            'application/powerpoint',
            'application/vnd.ms-powerpoint',
        ),
        'tar' => 'application/x-tar',
        'rar' => array(
            'application/octet-stream',
            'application/x-rar',
            'application/x-rar-compressed',
            'application/rar', //兼容zip
            'application/x-zip',
            'application/zip',
            'application/x-zip-compressed',
        ),
        'tgz' => array(
            'application/x-tar',
            'application/x-gzip-compressed',
        ),
        'xhtml' => 'application/xhtml+xml',
        'xht' => 'application/xhtml+xml',
        'zip' => array(
            'application/x-zip',
            'application/zip',
            'application/x-zip-compressed', //兼容rar
            'application/octet-stream',
            'application/x-rar',
            'application/x-rar-compressed',
            'application/rar',
        ),
        'bmp' => array(
            'image/bmp',
            'image/x-windows-bmp',
            'image/x-ms-bmp',
        ),
        'gif' => 'image/gif',
        'jpeg' => array(
            'image/jpeg',
            'image/pjpeg',
            'image/gif',
            'image/png',
        ),
        'jpg' => array(
            'image/jpeg',
            'image/pjpeg',
            'image/gif',
            'image/png',
        ),
        'jpe' => array(
            'image/jpeg',
            'image/pjpeg',
        ),
        'png' => array(
            'image/png',
            'image/x-png',
        ),
        'html' => 'text/html',
        'htm' => 'text/html',
        'shtml' => 'text/html',
        'txt' => 'text/plain',
        'text' => 'text/plain',
        'doc' => 'application/msword',
        'docx' => array(
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip',
            'application/msword',
        ),
        'xlsx' => array(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip',
            'application/vnd.ms-excel',
            'application/msexcel',
            'application/octet-stream',
            'application/msword',
        ),
        'xl' => 'application/excel',
        'pdf' => array(
            'application/pdf',
            'application/x-download',
        ),
    );

    /**
     * 错误信息
     * @var type
     */
    private $_error = '';

    /**
     * 上传的文件信息
     * @var type
     */
    private $_uploadFileInfo;

    /**
     * 魔术方法，获取配置文件的配置项
     * @param string $name      配置项名称
     * @return string           配置项值
     */
    public function __get($name) {
        if (isset($this->_config[$name])) {
            return $this->_config[$name];
        }
        return null;
    }

    /**
     * 魔术方法，设置配置项的值
     * @param type $name
     * @param type $value
     */
    public function __set($name, $value) {
        if (isset($this->_config[$name])) {
            $this->_config[$name] = $value;
        }
    }

    /**
     * 魔术方法，验证配置项是否存在
     * @param type $name
     * @return type
     */
    public function __isset($name) {
        return isset($this->_config[$name]);
    }

    /**
     * 构造函数，用于生成配置项
     * @access public
     * @param array $config  上传参数
     */
    public function __construct($config = array()) {
        if (is_array($config)) {
            $this->_config = array_merge($this->_config, $config);
        }
    }

    /**
     * 上传单个上传字段中的文件 支持多附件
     * @access public
     * @param string 上传的文件名  上传文件信息
     * @param string $savePath  上传文件保存路径
     * @return boolean          上传结果
     */
    public function upload($attachName, $savePath = '') {
        //如果不指定保存文件名，则由系统默认
        if (empty($savePath)) {
            $savePath = $this->savePath;
        }

        // 检查上传目录
        if (!is_dir($savePath)) {
            // 尝试创建目录
            if (!mkdir($savePath, 0777, true)) {
                $this->_error = '上传目录' . $savePath . '不存在';
                return false;
            }
        } else {
            if (!is_writeable($savePath)) {
                $this->_error = '上传目录' . $savePath . '不可写';
                return false;
            }
        }
        $file = $_FILES[$attachName];
        //过滤无效的上传
        if (empty($file['name'])) {
            $this->_error = '没有选择上传文件';
            return false;
        }
        if (is_array($file['name'])) {
            //多文件上传，默认取第一个上传的文件
            $keys = array_keys($file);
            $count = count($file['name']);
            for ($i = 0; $i < $count; $i++) {
                foreach ($keys as $key) {
                    $fileArray[$i][$key] = $file[$key][$i];
                }

            }
            $file = array_shift($fileArray);
        }
        //登记上传文件的扩展信息
        $file['extension'] = $this->getExt($file['name']);
        $file['savepath'] = $savePath;
        $file['savename'] = $this->getSaveName($file);
        // 自动检查附件
        if ($this->autoCheck) {
            if (!$this->check($file)) {
                return false;
            }

        }
        //保存上传文件
        if (!$this->save($file)) {
            return false;
        }

        if (function_exists($this->hashType)) {
            $fun = $this->hashType;
            $file['hash'] = $fun($file['savepath'] . $file['savename']);
        }
        unset($file['tmp_name'], $file['error']);
        // 返回上传的文件信息
        $this->_uploadFileInfo = $file;
        return true;
    }

    /**
     * 多文件上传
     * @param string $savePath      文件保存路径
     * @return boolean              文件上传结果
     */
    public function uploadMulti($savePath = '') {
        //如果不指定保存文件名，则由系统默认
        if (empty($savePath)) {
            $savePath = $this->savePath;
        }

        // 检查上传目录
        if (!is_dir($savePath)) {
            // 尝试创建目录
            if (!mkdir($savePath)) {
                $this->_error = '上传目录' . $savePath . '不存在';
                return false;
            }
        } else {
            if (!is_writeable($savePath)) {
                $this->_error = '上传目录' . $savePath . '不可写';
                return false;
            }
        }
        $fileInfo = array();
        $isUpload = false;

        // 获取上传的文件信息
        // 对$_FILES数组信息处理
        $files = $this->dealFiles($_FILES);
        foreach ($files as $key => $file) {
            //过滤无效的上传
            if (!empty($file['name'])) {
                //登记上传文件的扩展信息
                if (!isset($file['key'])) {
                    $file['key'] = $key;
                }

                $file['extension'] = $this->getExt($file['name']);
                $file['savepath'] = $savePath;
                $file['savename'] = $this->getSaveName($file);

                // 自动检查附件
                if ($this->autoCheck) {
                    if (!$this->check($file)) {
                        return false;
                    }

                }

                //保存上传文件
                if (!$this->save($file)) {
                    return false;
                }

                if (function_exists($this->hashType)) {
                    $fun = $this->hashType;
                    $file['hash'] = $fun($file['savepath'] . $file['savename']);
                }
                //上传成功后保存文件信息，供其他地方调用
                unset($file['tmp_name'], $file['error']);
                $fileInfo[] = $file;
                $isUpload = true;
            }
        }
        if ($isUpload) {
            $this->_uploadFileInfo = $fileInfo;
            return true;
        } else {
            $this->error = '没有选择上传文件';
            return false;
        }
    }

    /**
     * 转换上传文件数组变量为正确的方式
     * @access private
     * @param array $files  上传的文件变量
     * @return array
     */
    private function dealFiles($files) {
        $fileArray = array();
        $n = 0;
        foreach ($files as $key => $file) {
            if (is_array($file['name'])) {
                $keys = array_keys($file);
                $count = count($file['name']);
                for ($i = 0; $i < $count; $i++) {
                    $fileArray[$n]['key'] = $key;
                    foreach ($keys as $_key) {
                        $fileArray[$n][$_key] = $file[$_key][$i];
                    }
                    $n++;
                }
            } else {
                $fileArray[$key] = $file;
            }
        }
        return $fileArray;
    }

    /**
     * 处理单个文件
     * @access public
     * @param mixed $file 上传的文件数据
     * @return boolean  处理结果
     */
    private function save($file) {
        $filename = $file['savepath'] . $file['savename'];
        if (!$this->uploadReplace && is_file($filename)) {
            // 不覆盖同名文件
            $this->_error = '文件已经存在！' . $filename;
            return false;
        }
        // 如果是图像文件 检测文件格式
        if (in_array(strtolower($file['extension']), array('gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf'))) {
            $info = getimagesize($file['tmp_name']);
            if (false === $info || ('gif' == strtolower($file['extension']) && empty($info['bits']))) {
                $this->_error = '非法图像文件';
                return false;
            }
        }
        if (!move_uploaded_file($file['tmp_name'], $filename)) {
            $this->_error = '文件上传保存错误！';
            return false;
        }
        return true;
    }

    /**
     * 获取错误信息
     * @access public
     * @return string       错误信息
     */
    public function getErrMsg() {
        return $this->_error;
    }

    /**
     * 获取上传的文件信息
     * @access public
     * @return array
     */
    public function getUploadFileInfo() {
        return $this->_uploadFileInfo;
    }

    /**
     * 获取文件后缀名
     * @access private
     * @param string $fileName      文件名
     * @return string       文件后缀
     */
    private function getExt($fileName) {
        $pathInfo = pathinfo($fileName);
        return $pathInfo['extension'];
    }

    /**
     * 检验文件后缀
     * @access private
     * @param string $ext   文件后缀
     * @return boolean      文件后缀是否通过检查
     */
    private function chkExt($ext) {
        if (!empty($this->allowTypes)) {
            return in_array(strtolower($ext), $this->allowTypes, true);
        }

        return true;
    }

    /**
     * 检查文件类型
     * @access private
     * @param sting $type   文件类型
     * @return boolean      文件是否通过类型检查
     */
    private function chkType($ext, $type) {
        if (!isset($this->_mimes[$ext])) {
            return false;
        }
        if (!empty($this->allowTypes)) {
            $mimes = is_array($this->_mimes[$ext]) ? $this->_mimes[$ext] : array($this->_mimes[$ext]);
            return in_array(strtolower($type), $mimes);
        }
        return true;
    }

    /**
     * 检查文件大小,如果配置项中定义最大大小为-1，不进行检查，直接通过
     * @access private
     * @param int $size 当前文件大小
     * @return boolean      文件是否通过大小检查
     */
    private function chkSize($size) {
        return !($size > $this->maxSize) || (-1 == $this->maxSize);
    }

    /**
     * 判断文件是否通过HTTP POST上传，即是否合法上传
     * @access private
     * @param string $fileName
     * @return boolean 是否合法上传
     */
    private function chkUpload($fileName) {
        return is_uploaded_file($fileName);
    }

    /**
     * 根据上传文件命名规则取得保存文件名
     * @access private
     * @param string $file  文件数据
     * @return string           新文件名
     */
    private function getSaveName($file) {
        $rule = $this->saveRule;
        if (empty($rule)) {
            //没有定义命名规则，则保持文件名不变
            $saveName = $file['name'];
        } else {
            if (function_exists($rule)) {
                //使用函数生成一个唯一文件标识号
                $saveName = $rule() . "." . $file['extension'];
            } else {
                //使用给定的文件名作为标识号
                $saveName = $rule . time() . rand(0, 1000) . "." . $file['extension'];
            }
        }
        if ($this->autoSub) {
            // 使用子目录保存文件
            $file['savename'] = $saveName;
            $saveName = $this->getSubName($file) . $saveName;
        }
        return $saveName;
    }

    /**
     * 获取子目录的名称
     * @access private
     * @param array $file  文件数据
     * @return string       子目录名称
     */
    private function getSubName($file) {
        switch ($this->subType) {
        case 'custom':
            $dir = empty($this->subDir) ? date($this->dateFormat, time()) : $this->subDir;
            break;
        case 'date':
            $dir = date($this->dateFormat, time()) . '/';
            break;
        case 'hash':
        default:
            $name = md5($file['savename']);
            $dir = '';
            for ($i = 0; $i < $this->hashLevel; $i++) {
                $dir .= $name{$i} . '/';
            }
            break;
        }
        if (!is_dir($file['savepath'] . $dir)) {
            mkdir($file['savepath'] . $dir, 0777, true);
        }
        return $dir;
    }

    /**
     * 检查上传的文件
     * @access private
     * @param array $file 文件信息
     * @return boolean   文件是否通过检查
     */
    private function check($file) {
        if ($file['error'] !== 0) {
            //文件上传失败
            //捕获错误代码
            $this->error($file['error']);
            return false;
        }
        //文件上传成功，进行自定义规则检查
        //检查是否合法上传
        if (!$this->chkUpload($file['tmp_name'])) {
            $this->_error = '非法上传文件！';
            return false;
        }
        //检查文件类型
        if (!$this->chkExt($file['extension'])) {
            $this->_error = '上传文件类型不允许';
            return false;
        }
        //检查文件Mime类型
        if (!$this->chkType($file['extension'], $file['type'])) {
            $this->_error = '上传文件类型不允许！';
            return false;
        }
        //检查文件大小
        if (!$this->chkSize($file['size'])) {
            $this->_error = '上传文件大小不符！';
            return false;
        }
        return true;
    }

    /**
     * 获取错误代码信息
     * @access protected
     * @param string $errorNo  错误号码
     * @return void
     */
    protected function error($errorNo) {
        switch ($errorNo) {
        case 1:
            $this->_error = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值';
            break;
        case 2:
            $this->_error = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值';
            break;
        case 3:
            $this->_error = '文件只有部分被上传';
            break;
        case 4:
            $this->_error = '没有文件被上传';
            break;
        case 6:
            $this->_error = '找不到临时文件夹';
            break;
        case 7:
            $this->_error = '文件写入失败';
            break;
        default:
            $this->_error = '未知上传错误！';
        }
        return;
    }

}
