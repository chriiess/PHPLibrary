<?php
/**
 *
 */
class uploadClass {
	//上传文件保存的路径
	private $path = "./uploads";
	//设置限制上传文件的类型
	private $allowType = ['jpg', 'gif', 'png'];
	//限制文件上传大小（字节） 2M
	private $maxSize = '2097152';
	//设置是否随机重命名文件， false不随机
	private $isRandName = true;

	//源文件名
	private $originName;
	//临时文件名
	private $tmpFileName;
	//文件类型(文件后缀)
	private $fileType;
	//文件大小
	private $fileSize;
	//新文件名
	private $newFileName;
	//错误号
	private $errorNum = 0;
	//错误报告消息
	private $errorMessage = "";

	/**
	 * 设置上传路径
	 * @设置上传路径
	 * @Author      dong
	 * @DateTime    2016-09-03T16:01:01+0800
	 * @param       [string]
	 *              $path [上传路径]
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * 设置允许文件上传类型
	 * @description
	 * @Author      dong
	 * @DateTime    2016-09-03T16:08:45+0800
	 * @param       [type]                   $allowType [description]
	 */
	public function setAllowType($allowType) {
		$this->allowType = $allowType;
	}

	/**
	 * 设置文件上传大小限制
	 * @description
	 * @Author      dong
	 * @DateTime    2016-09-03T16:26:48+0800
	 * @param       int                   $maxSize 文件大小
	 */
	public function setMaxSize($maxSize) {
		$this->MaxSize = $maxSize;
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
	 * 上传文件
	 * @description
	 * @Author      dong
	 * @DateTime    2016-09-03T17:13:22+0800
	 * @param       [string]                   $fileField 文件
	 * @return      [bool]                              [如果上传成功返回数true]
	 */
	public function upload($fileField) {
		$return = true;
		// 检查文件路径是否合法
		if (!$this->checkFilePath()) {
			$this->errorMessage = $this->getError();
			return false;
		}
		// 将文件上传的信息取出赋给变量
		$name = $_FILES[$fileField]['name'];
		$tmp_name = $_FILES[$fileField]['tmp_name'];
		$size = $_FILES[$fileField]['size'];
		$error = $_FILES[$fileField]['error'];
		// 如果是多个文件上传则$file["name"]会是一个数组
		if (is_Array($name)) {
			$errors = array();
			// 多个文件上传则循环处理,这个循环只有检查上传的大小和类型是否合法，并没有真正上传
			for ($i = 0; $i < count($name); $i++) {
				// 设置文件信息
				if ($this->setFiles($name[$i], $tmp_name[$i], $size[$i], $error[$i])) {
					if (!$this->checkFileSize() || !$this->checkFileType()) {
						$errors[] = $this->getError();
						$return = false;
					}
				} else {
					$errors[] = $this->getError();
					$return = false;
				}
				// 如果有问题，则重新初使化属性
				if (!$return) {
					$this->setFiles();
				}

			}
			// 文件的大小和类型都合法
			if ($return) {
				// 存放所有上传后文件名的变量数组
				$fileNames = array();
				// 如果上传的多个文件都是合法的，则通过循环向服务器上传文件
				for ($i = 0; $i < count($name); $i++) {
					if ($this->setFiles($name[$i], $tmp_name[$i], $size[$i], $error[$i])) {
						$this->setNewFileName();
						if (!$this->copyFile()) {
							$errors[] = $this->getError();
							$return = false;
						}
						$fileNames[] = $this->newFileName;
					}
				}
				$this->newFileName = $fileNames;
			}
			$this->errorMessage = $errors;
			return $return;
			// 上传单个文件处理方法
		} else {
			// 设置文件信息
			if ($this->setFiles($name, $tmp_name, $size, $error)) {
				// 上传之前先检查一下大小和类型
				if ($this->checkFileSize() && $this->checkFileType()) {
					// 为上传文件设置新文件名
					$this->setNewFileName();
					// 上传文件 
					if ($this->copyFile()) {
						return true;
					} else {
						$return = false;
					}
				} else {
					$return = false;
				}
			} else {
				$return = false;
			}
			//如果$return为false, 则出错，将错误信息保存在属性errorMessage中
			if (!$return) {
				$this->errorMessage = $this->getError();
			}

			return $return;
		}
	}

	/**
	 * 获取上传后的文件名称
	 * @param  void   没有参数
	 * @return string 上传后，新文件的名称， 如果是多文件上传返回数组
	 */
	public function getFileName() {
		return $this->newFileName;
	}

	/**
	 * 上传失败后，调用该方法则返回，上传出错信息
	 * @param  void   没有参数
	 * @return string  返回上传文件出错的信息报告，如果是多文件上传返回数组
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
		$str = "上传文件{$this->originName}时出错 : ";
		switch ($this->errorNum) {
		case 4:$str .= "没有文件被上传";
			break;
		case 3:$str .= "文件只有部分被上传";
			break;
		case 2:$str .= "上传文件的大小超过了HTML表单中MAX_FILE_SIZE选项指定的值";
			break;
		case 1:$str .= "上传的文件超过了php.ini中upload_max_filesize选项限制的值";
			break;
		case -1:$str .= "未允许类型";
			break;
		case -2:$str .= "文件过大,上传的文件大小为{$this->fileSize}不能超过{$this->maxSize}个字节";
			break;
		case -3:$str .= "上传失败";
			break;
		case -4:$str .= "建立存放上传文件目录失败，请重新指定上传目录";
			break;
		case -5:$str .= "必须指定上传文件的路径";
			break;
		default:$str .= "未知错误";
		}
		return $str;
	}

	/**
	 * 设置当前上传的文件的信息
	 * @description
	 * @Author      dong
	 * @DateTime    2016-09-04T14:47:55+0800
	 * @param       string                   $name     文件原名称
	 * @param       string                   $tmp_name $_FILES中的临时名称
	 * @param       integer                  $size     大小
	 * @param       integer                  $error    $_FILES中的错误信息
	 * @return      bool                     是否没有错误
	 */
	private function setFiles($name = "", $tmp_name = "", $size = 0, $error = 0) {
		$this->setOption('errorNum', $error);
		if ($error) {
			return false;
		}
		$this->setOption('originName', $name);
		$this->setOption('tmpFileName', $tmp_name);
		$this->setOption('fileType', $this->getExt($this->originName));
		$this->setOption('fileSize', $size);
		return true;
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
	 * 设置上传后文件的名称
	 * @description
	 * @Author      dong
	 * @DateTime    2016-09-04T14:52:51+0800
	 */
	private function setNewFileName() {
		if ($this->isRandName) {
			$this->setOption('newFileName', $this->setRandName());
		} else {
			$this->setOption('newFileName', $this->originName);
		}
	}

	/**
	 * 检查文件类型是否合法
	 * @description
	 * @Author      dong
	 * @DateTime    2016-09-04T14:53:22+0800
	 * @return      bool                   true为合法，false为非法
	 */
	private function checkFileType() {
		if (in_array(strtolower($this->fileType), $this->allowType)) {
			return true;
		} else {
			$this->setOption('errorNum', -1);
			return false;
		}
	}

	/**
	 * 检查文件大小是否合法
	 * @description
	 * @Author      dong
	 * @DateTime    2016-09-04T14:53:22+0800
	 * @return      bool                   true为合法，false为非法
	 */
	private function checkFileSize() {
		if ($this->fileSize > $this->maxSize) {
			$this->setOption('errorNum', -2);
			return false;
		} else {
			return true;
		}
	}

	/**
	 * 获取文件后缀
	 * @description
	 * @Author      dong
	 * @DateTime    2016-09-03T17:10:22+0800
	 * @param       string $file  文件名
	 * @return      string 文件后缀
	 */
	private function getExt($fileName) {
		return end(explode('.', $fileName));
	}

	/**
	 * 检查是否有存放上传文件的目录，有指定目录但是没有该目录则创建该目录
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
	 * 设置随机文件名字
	 * @description
	 * @Author      dong
	 * @DateTime    2016-09-04T14:54:34+0800
	 * @return      string                   文件的名字
	 */
	private function setRandName() {
		$fileName = date('YmdHis') . "_" . rand(100, 999);
		return $fileName . '.' . $this->fileType;
	}

	/**
	 * 从临时的位置复制到指定的位置
	 * @description
	 * @Author      dong
	 * @DateTime    2016-09-04T14:55:54+0800
	 * @return      bool                   true为成功，false为失败
	 */
	private function copyFile() {
		if (!$this->errorNum) {
			$path = rtrim($this->path, '/') . '/';
			$path .= $this->newFileName;
			if (@move_uploaded_file($this->tmpFileName, $path)) {
				return true;
			} else {
				$this->setOption('errorNum', -3);
				return false;
			}
		} else {
			return false;
		}
	}
}

?>