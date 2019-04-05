<?php
namespace support\upload;

use support\openmethod\parameter\File;
use suda\framework\filesystem\FileSystem;

/**
 * 上传处理工具
 */
class UploadUtil
{
    /**
     * 计算文件Hash
     *
     * @param string $path
     * @return boolean
     */
    public static function hash(string $path)
    {
        return \str_replace(['+','/', '='], ['_','__',''], \base64_encode(\md5_file($path, true)));
    }

    /**
     * 保存文件
     *
     * @param \support\openmethod\parameter\File $file
     * @return string
     */
    public static function save(File $file):string
    {
        $hash = static::hash($file->getPathname());
        $extension = strtolower($file->getExtension());
        if ($file->isImage()) {
            $path = 'image/'.$extension;
        } else {
            $path = $extension;
        }
        $path .='/'.$hash.'/0.jpg';
        $save = SUDA_DATA.'/upload/'.$path;
        FileSystem::make(dirname($save));
        FileSystem::copy($file->getPathname(), $save);
        return $path;
    }
}
