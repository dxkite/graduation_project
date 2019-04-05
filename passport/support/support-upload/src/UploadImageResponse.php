<?php
namespace support\upload\response;

use suda\framework\Request;
use suda\framework\Response;
use suda\application\Resource;
use suda\application\Application;
use suda\framework\filesystem\FileSystem;
use suda\application\processor\RequestProcessor;
use suda\application\processor\FileRangeProccessor;

/**
 * 上传文件显示
 */
class UploadImageResponse implements RequestProcessor
{
    public function onRequest(Application $application, Request $request, Response $response)
    {
        //  /upload/image/hash/100x100.jpg
        $hash = $request->get('hash');
        $type = $request->get('type');
        $options = $request->get('options');
        $extension = pathinfo($options, PATHINFO_EXTENSION);
        $resource = new Resource([ SUDA_DATA.'/upload' ]);
        $path = $resource->getResourcePath('image/'.$type.'/'.$hash.'.'.$type);
        if ($path) {
            if (FileSystem::isWritable(SUDA_PUBLIC.'/upload')) {
                $savePath = SUDA_PUBLIC.'/upload/image/'.$type.'/'.$hash.'/'.$options;
                FileSystem::make(dirname($savePath));
                FileSystem::copy($path, $savePath);
            }
            return (new FileRangeProccessor($path))->onRequest($application, $request, $response);
        }
        $response->status(404);
    }
}
