<?php

/**
 * Description of FileDownload
 *  PHP文件下载类
 * @author albafica.wang
 * @createdate 2014/12/29
 */
class FileDownload {

    private $filter = array(); //过滤器，标志禁止下载文件类型，为空则可以下载所有文件
    private $errMsg = ''; //错误信息
    private $mineType = ''; //文件拓展类型
    private $fileType = array();

    public function __construct($fileFilter = '') {
        $this->setFilter($fileFilter);
        $this->setFileType();
    }

    /**
     * 下载文件
     * @param string $filePath              文件路径
     * @param string $fileName              文件保存名，如果为空，则从路径中获取保存的名字
     */
    public function downloadFile($filePath, $downLoadName = '') {
        //文件验证失败
        if (!$this->fileCheck($filePath)) {
            return false;
        }
        if (empty($downLoadName)) {
            $downLoadNameArr = explode('/', strtr($filePath, '\\', '/'));
            $downLoadName = array_pop($downLoadNameArr);
        }
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Component: must-revalidate, post-check=0, pre-check=0");
        header("Content-type:" . $this->mineType);
        header("Content-Length: " . filesize($filePath));
        header("Content-Disposition: attachment; filename=\"$downLoadName\"");
        header('Content-Transfer-Encoding: binary');
        readfile($filePath);
        return true;
    }

    /**
     * 设置文件过滤器，定义禁止下载的文件类型,可以在下载前动态定义
     * @param string $fileFilter            字符串定义的文件下载类型，用逗号分隔
     */
    public function setFilter($fileFilter) {
        if (empty($fileFilter)) {
            return;
        }
        $this->filter = explode(',', strtolower($fileFilter));
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getErrMsg() {
        return $this->errMsg;
    }

    /**
     * 验证文件有效性及可下载
     * @param string $filePath      文件路径
     * @return boolean
     */
    private function fileCheck($filePath) {
        if (!file_exists($filePath)) {
            $this->errMsg = $filePath . '不存在!';
            return false;
        }
        //验证文件过滤器，查看文件是否在禁止下载列表中
        $filetypeArr = explode('.', $filePath);
        $filetype = strtolower(array_pop($filetypeArr));
        if (in_array($filetype, $this->filter)) {
            $this->errMsg = $filePath . '不允许下载！';
            return false;
        }
        if (function_exists("finfo_open")) {
            $finfo = finfo_open(FILEINFO_MIME);
            $this->mineType = finfo_file($finfo, $filePath);
            finfo_close($finfo);
        }
        if (empty($this->mineType) && isset($this->fileType[$filetype])) {
            $this->mineType = $this->fileType[$filetype];
        }
        if (empty($this->mineType)) {
            $this->errMsg = '获取' . $filePath . '文件类型时候发生错误，或者不存在预定文件类型内';
            return false;
        }
        return true;
    }

    /**
     * 设置文件类型拓展,用于验证文件类型是否正确
     */
    private function setFileType() {
        $this->fileType['chm'] = 'application/octet-stream';
        $this->fileType['ppt'] = 'application/vnd.ms-powerpoint';
        $this->fileType['xls'] = 'application/vnd.ms-excel';
        $this->fileType['doc'] = 'application/msword';
        $this->fileType['exe'] = 'application/octet-stream';
        $this->fileType['rar'] = 'application/octet-stream';
        $this->fileType['js'] = "javascript/js";
        $this->fileType['css'] = "text/css";
        $this->fileType['hqx'] = "application/mac-binhex40";
        $this->fileType['bin'] = "application/octet-stream";
        $this->fileType['oda'] = "application/oda";
        $this->fileType['pdf'] = "application/pdf";
        $this->fileType['ai'] = "application/postsrcipt";
        $this->fileType['eps'] = "application/postsrcipt";
        $this->fileType['es'] = "application/postsrcipt";
        $this->fileType['rtf'] = "application/rtf";
        $this->fileType['mif'] = "application/x-mif";
        $this->fileType['csh'] = "application/x-csh";
        $this->fileType['dvi'] = "application/x-dvi";
        $this->fileType['hdf'] = "application/x-hdf";
        $this->fileType['nc'] = "application/x-netcdf";
        $this->fileType['cdf'] = "application/x-netcdf";
        $this->fileType['latex'] = "application/x-latex";
        $this->fileType['ts'] = "application/x-troll-ts";
        $this->fileType['src'] = "application/x-wais-source";
        $this->fileType['zip'] = "application/zip";
        $this->fileType['bcpio'] = "application/x-bcpio";
        $this->fileType['cpio'] = "application/x-cpio";
        $this->fileType['gtar'] = "application/x-gtar";
        $this->fileType['shar'] = "application/x-shar";
        $this->fileType['sv4cpio'] = "application/x-sv4cpio";
        $this->fileType['sv4crc'] = "application/x-sv4crc";
        $this->fileType['tar'] = "application/x-tar";
        $this->fileType['ustar'] = "application/x-ustar";
        $this->fileType['man'] = "application/x-troff-man";
        $this->fileType['sh'] = "application/x-sh";
        $this->fileType['tcl'] = "application/x-tcl";
        $this->fileType['tex'] = "application/x-tex";
        $this->fileType['texi'] = "application/x-texinfo";
        $this->fileType['texinfo'] = "application/x-texinfo";
        $this->fileType['t'] = "application/x-troff";
        $this->fileType['tr'] = "application/x-troff";
        $this->fileType['roff'] = "application/x-troff";
        $this->fileType['shar'] = "application/x-shar";
        $this->fileType['me'] = "application/x-troll-me";
        $this->fileType['ts'] = "application/x-troll-ts";
        $this->fileType['gif'] = "image/gif";
        $this->fileType['jpeg'] = "image/pjpeg";
        $this->fileType['jpg'] = "image/pjpeg";
        $this->fileType['jpe'] = "image/pjpeg";
        $this->fileType['ras'] = "image/x-cmu-raster";
        $this->fileType['pbm'] = "image/x-portable-bitmap";
        $this->fileType['ppm'] = "image/x-portable-pixmap";
        $this->fileType['xbm'] = "image/x-xbitmap";
        $this->fileType['xwd'] = "image/x-xwindowdump";
        $this->fileType['ief'] = "image/ief";
        $this->fileType['tif'] = "image/tiff";
        $this->fileType['tiff'] = "image/tiff";
        $this->fileType['pnm'] = "image/x-portable-anymap";
        $this->fileType['pgm'] = "image/x-portable-graymap";
        $this->fileType['rgb'] = "image/x-rgb";
        $this->fileType['xpm'] = "image/x-xpixmap";
        $this->fileType['txt'] = "text/plain";
        $this->fileType['c'] = "text/plain";
        $this->fileType['cc'] = "text/plain";
        $this->fileType['h'] = "text/plain";
        $this->fileType['html'] = "text/html";
        $this->fileType['htm'] = "text/html";
        $this->fileType['htl'] = "text/html";
        $this->fileType['rtx'] = "text/richtext";
        $this->fileType['etx'] = "text/x-setext";
        $this->fileType['tsv'] = "text/tab-separated-values";
        $this->fileType['mpeg'] = "video/mpeg";
        $this->fileType['mpg'] = "video/mpeg";
        $this->fileType['mpe'] = "video/mpeg";
        $this->fileType['avi'] = "video/x-msvideo";
        $this->fileType['qt'] = "video/quicktime";
        $this->fileType['mov'] = "video/quicktime";
        $this->fileType['moov'] = "video/quicktime";
        $this->fileType['movie'] = "video/x-sgi-movie";
        $this->fileType['au'] = "audio/basic";
        $this->fileType['snd'] = "audio/basic";
        $this->fileType['wav'] = "audio/x-wav";
        $this->fileType['aif'] = "audio/x-aiff";
        $this->fileType['aiff'] = "audio/x-aiff";
        $this->fileType['aifc'] = "audio/x-aiff";
        $this->fileType['swf'] = "application/x-shockwave-flash";
    }

}
