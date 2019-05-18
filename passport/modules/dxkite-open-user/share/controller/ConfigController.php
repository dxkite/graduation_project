<?php


namespace dxkite\openuser\controller;


use suda\application\Resource;
use suda\framework\Config;
use suda\framework\filesystem\FileSystem;

class ConfigController
{
    public static function loadSMTPConfig(string $dataPath): array {
        $resource = new Resource($dataPath);
        $default = [
            'server' => 'smtp.qq.com',
            'port' => 465,
            'name' => '',
            'password' => '',
            'timeout' => 3,
            'security' => true,
        ];
        $path = $resource->getConfigResourcePath('config/mailer-smtp');
        if ($path !== null) {
            $config = Config::loadConfig($path);
            if (is_array($config)) {
                return array_merge($default, $config);
            }
        }
        return $default;
    }

    /**
     * 保存配置
     * @param string $dataPath
     * @param array $config
     * @return bool
     */
    public static function saveSTMPConfig(string $dataPath, array $config) {
        $preview = static::loadSMTPConfig($dataPath);
        $resource = new Resource($dataPath);
        $path = $resource->getConfigResourcePath('config/mailer-smtp');
        $savePath = $path ?? $dataPath.'/config/mailer-smtp.json';
        $config = array_merge($preview, $config);
        return FileSystem::put($savePath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * 加载腾讯短信配置
     * @param string $dataPath
     * @return array
     */
    public static function loadTencentSMSConfig(string $dataPath) {
        $resource = new Resource($dataPath);
        $default = [
            'app-id' => '',
            'app-key' => '',
            'sign' => '',
            'template' => '',
        ];
        $path = $resource->getConfigResourcePath('config/tencent-sms');
        if ($path !== null) {
            $config = Config::loadConfig($path);
            if (is_array($config)) {
                return array_merge($default, $config);
            }
        }
        return $default;
    }

    /**
     * 保存配置
     * @param string $dataPath
     * @param array $config
     * @return bool
     */
    public static function saveTencentSMSConfig(string $dataPath, array $config) {
        $preview = static::loadTencentSMSConfig($dataPath);
        $resource = new Resource($dataPath);
        $path = $resource->getConfigResourcePath('config/tencent-sms');
        $savePath = $path ?? $dataPath.'/config/tencent-sms.json';
        $config = array_merge($preview, $config);
        return FileSystem::put($savePath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}