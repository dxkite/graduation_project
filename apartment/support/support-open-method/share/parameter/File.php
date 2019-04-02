<?php
namespace support\openmethod\parameter;

use suda\framework\http\UploadedFile;

/**
 * 表单文件
 */
class File extends UploadedFile
{
    /**
     * 是否是图片
     *
     * @var bool
     */
    protected $image;

    /**
     * 扩展名
     *
     * @var string
     */
    protected $extension;

    public function __construct(UploadedFile $file)
    {
        parent::__construct($file->getPathname(), $file->$this->originalName(), $file->getMimeType(), $file->getError());
        $this->image = $this->guessImage();
    }

    /**
     * 推测图片类型
     *
     * @return bool
     */
    protected function guessImage():bool
    {
        $type = strtolower($this->getExtension());
        if (preg_match('/image\/*/i', $this->mimeType) || in_array($type, ['swf','jpc','jbx','jb2','swc'])) {
            $imageType = false;
            if (function_exists('exif_imagetype')) {
                $imageType = exif_imagetype($this->getPathname());
            } else {
                $value = getimagesize($this->getPathname());
                if ($value) {
                    $imageType = $value[2];
                }
            }
            if ($imageType) {
                $this->mimeType = image_type_to_mime_type($imageType);
                $this->extension =  image_type_to_extension($imageType, false);
                return true;
            }
        }
        return false;
    }

    /**
     * 获取扩展名
     *
     * @return string
     */
    public function getExtension():string {
        return $this->extension ?? parent::getExtension();
    }

    /**
     * 获取是否为图片
     *
     * @return boolean
     */
    public function isImage():bool {
        return $this->image;
    }
}
