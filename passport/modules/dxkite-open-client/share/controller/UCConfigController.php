<?php


namespace dxkite\openclient\controller;

use suda\application\Resource;
use suda\framework\Config;
use suda\framework\filesystem\FileSystem;

class UCConfigController
{
    /**
     * 加载配置
     * @param string $dataPath
     * @return array
     */
    public function loadConfig(string $dataPath) {
        $resource = new Resource($dataPath);
        $path = $resource->getConfigResourcePath('config/open-user');
        $config = [
            'appid' => '',
            'secret' => '',
            'server' => '',
            'signin' => '{server}/open-user/oauth2/authorize?redirect_uri={redirect_uri}&appid={appid}',
            'access_token' => '{server}/open-user/oauth2/access_token?appid={appid}&secret={secret}&code={code}',
            'refresh_token' => '{server}/open-user/oauth2/refresh_token?appid={appid}&refresh_token={refresh_token}',
            'userinfo' => '{server}/open-user/oauth2/userinfo?user={user}&access_token={access_token}'
        ];
        if ($path !== null) {
            return array_merge($config, Config::loadConfig($path) ?? []);
        }
        return $config;
    }

    /**
     * 保存配置
     * @param string $dataPath
     * @param array $config
     * @return bool
     */
    public function saveConfig(string $dataPath, array $config) {
        $preview = $this->loadConfig($dataPath);
        $resource = new Resource($dataPath);
        $path = $resource->getConfigResourcePath('config/open-user');
        $savePath = $path ?? $dataPath.'/config/open-user.json';
        $config = array_merge($preview, $config);
        return FileSystem::put($savePath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}