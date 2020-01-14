<?php

/**
 * 檔案上傳
 */
class Upload
{
    /**
     * 回傳訊息
     */
    protected $returnMessage = false;

    /**
     * 檢查次數
     */
    protected $repeatCount = 50;

    /**
     * 上傳位置
     */
    protected $uploadPath = './public/uploads/web/';

    /**
     * 檔案類型
     */
    protected $typeOf = '';

    /**
     * 重新命名
     */
    protected $rename = false;

    /**
     * 檢查檔案名稱是否有特殊字元
     */
    protected $fileNameCharacter = false;

    /**
     * 回傳類型
     *
     * true : 檔名， false : 路徑
     */
    protected $returnNameType = 'path';

    /**
     * 允許檔案類型
     */
    protected $allowedType = '*';

    /**
     * 禁止上傳類型
     */
    protected $unallowedType = ['php', 'html', 'xml', 'asp', 'jspx', 'jsp'];

    /**
     * 圖片壓縮
     */
    protected $compress = false;

    /**
     * 壓縮比例
     */
    protected $compressRatio = 0.5;

    /**
     * 壓縮寬度
     */
    protected $width = '';

    /**
     * 壓縮高度
     */
    protected $height = '';

    /**
     * 變數初始設定
     *
     * @param array $config
     */
    private function init($config)
    {
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * 執行
     *
     * @param string $name
     * @param array $config
     */
    public function run($name = '', $config = [])
    {
        $this->init($config);

        if ($this->formUploadCheck($name) !== true) {
            return $this->getErrorMessage();
        }

        list($fileName, $fileType) = explode('.', $_FILES[$name]['name']);

        if ($this->fileTypeCheck($fileType) !== true) {
            return $this->getErrorMessage();
        }

        $this->uploadPathFolderCheck();

        $this->fileNameCharacterCheck($fileName);

        $fileNameNew = $this->getFileName($fileName, $fileType);
        $path = $this->uploadPath.$fileNameNew;

        if (is_file($path)) {
            for ($i=0; $i < $this->repeatCount ; $i++) {
                $fileNameNew = $this->getFileName($fileName, $fileType, $i);
                $path = $this->uploadPath.$fileNameNew;

                if (!is_file($path)) {
                    break;
                }
            }
        }

        if (move_uploaded_file($_FILES[$name]["tmp_name"], $path) === true) {
            if ($this->compress === true && $this->typeOf === 'img') {
                $this->compressImg($path, $this->width, $this->height);
            }

            return $this->returnNameType == 'name' ? $fileNameNew : $path;
        } else {
            return $this->getErrorMessage();
        }
    }

    /**
     * 檔案上傳檢查
     *
     * @param string $name
     */
    private function formUploadCheck($name)
    {
        if (!isset($_FILES[$name])) {
            return false;
        }

        if (empty($_FILES[$name]['name'])) {
            return false;
        }

        //判斷是否透過Http post上傳
        if (!is_uploaded_file($_FILES[$name]["tmp_name"])) {
            return false;
        }

        return true;
    }

    /**
     * 檔案副檔名檢查
     *
     * @param string $fileType
     */
    private function fileTypeCheck($fileType)
    {
        if (in_array(strtolower($fileType), $this->unallowedType)) {
            return false;
        }

        //判斷是否有自行輸入允許檔案類型
        if ($this->allowedType !== '*') {
            if (!in_array(strtolower($fileType), $this->allowedType)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 上傳位置檢查
     */
    private function uploadPathFolderCheck()
    {
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0777, true);
        }
    }

    /**
     * 檔案字元檢查
     */
    private function fileNameCharacterCheck($fileName)
    {
        if (mb_strlen($fileName, mb_detect_encoding($fileName)) != strlen($fileName)) {
            $this->fileNameCharacter = true;
        }
    }

    /**
     * 檔名命名
     *
     * @param string $fileName
     * @param string $fileType
     * @param int $num
     */
    private function getFileName($fileName, $fileType, $num = '')
    {
        $fileName = preg_replace('/-|\*|#/', '', $fileName).$num;

        if ($this->rename === true) {
            $fileName = date("Ymd").rand(0,9).rand(0,9).rand(0,9);
        } else {
            if ($this->fileNameCharacter === true) {
                $fileName = date("Ym").rand(0,9).rand(0,9).rand(0,9);
            }
        }

        return $fileName.'.'.strtolower($fileType);
    }

    /**
     * 圖片壓縮
     *
     * @param string $imgSrc
     * @param int $requestWidth
     * @param int $requestHeight
     */
    private function compressImg($imgSrc, $requestWidth, $requestHeight)
    {
        list($width, $height, $type) = getimagesize($imgSrc); //取檔案資訊

        //image_type_to_extension : 1=gif 2=jpg 3=png 4=swf 5=psd 6=bmp 7=tiff 9=jpc 10=jp2
        $imageinfo = [
            'width' => $width,
            'height' => $height,
            'type' => image_type_to_extension($type, false) //取檔案類型，不含.(false)
        ];

        if ($requestWidth == '' && $requestHeight == '') {
            $requestWidth = $width * $this->compressRatio;
            $requestHeight = $height * $this->compressRatio;
        }

        //僅支援格式
        $allowedCompressimgType = ['gif', 'jpg', 'png', 'bmp', 'jpeg'];

        if (in_array($imageinfo['type'], $allowedCompressimgType)) {
            $fun = "imagecreatefrom".$imageinfo['type'];
            $image = $fun($imgSrc);
            $imageThump = imagecreatetruecolor($requestWidth, $requestHeight);
            imagecopyresampled($imageThump, $image, 0, 0, 0, 0, $requestWidth, $requestHeight, $imageinfo['width'], $imageinfo['height']);
            imagedestroy($image);
            $image = $imageThump;
            $funcs = "image".$imageinfo['type'];
            $funcs($image, $imgSrc);
        }
    }

    /**
     * 錯誤訊息
     *
     * @param string $number
     */
    private function getErrorMessage($number = '')
    {
        if ($this->returnMessage === true) {
            return $number;
        } else {
            return '';
        }
    }

}
