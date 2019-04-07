<?php
namespace dxkite\apartment;

use suda\framework\Config;
use suda\application\Resource;
use suda\framework\filesystem\FileSystem;
use suda\framework\arrayobject\ArrayDotAccess;

/**
 * 设置文件
 */
class Setting
{
    /**
     * 配置信息
     *
     * @var array
     */
    protected $config;
    
    /**
     * 路径
     *
     * @var string
     */
    protected $path;

    public function __construct(string $name)
    {
        $resource = new Resource(SUDA_DATA);
        $path = $resource->getConfigResourcePath('config/'.$name);
        if ($path) {
            $this->config = Config::loadConfig($path);
            $this->path = $path;
        } else {
            $this->config = [];
            $this->path = SUDA_DATA.'/config/'.$name.'.json';
        }
    }

    /**
     * 获取配置
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get(string $name = null, $default = null)
    {
        if (null === $name) {
            return $this->config;
        }
        return ArrayDotAccess::get($this->config, $name, $default);
    }

    /**
     * 设置配置
     *
     * @param string $name
     * @param mixed $value
     * @return array
     */
    public function set(string $name, $value)
    {
        return ArrayDotAccess::set($this->config, $name, $value);
    }

    /**
     * 判断配置是否存在
     *
     * @param string $name
     * @return boolean
     */
    public function has(string $name)
    {
        return ArrayDotAccess::exist($this->config, $name);
    }

    /**
     * 保存到文件
     *
     * @return bool
     */
    public function save():bool {
        return FileSystem::put($this->path, \json_encode($this->config, JSON_UNESCAPED_UNICODE| JSON_PRETTY_PRINT));
    }
}
