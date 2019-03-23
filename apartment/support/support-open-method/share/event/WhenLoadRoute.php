<?php
namespace support\openmethod\event;

use suda\framework\Route;
use suda\framework\Config;
use suda\application\Module;
use suda\application\Application;
use support\openmethod\processor\MethodInterfaceProcessor;

class WhenLoadRoute
{
    public function prepareRoute(Application $application, string $moduleFullName, Module $module, array $routeConfig)
    {
        foreach ($routeConfig as $name => $config) {
            $exname = $moduleFullName.':'.$name;
            $method = $config['method'] ?? [];
            $attriute = [];
            $attriute['module'] = $moduleFullName;
            $attriute['open-method'] = $config['class'] ?? [];
            $config['class'] = MethodInterfaceProcessor::class;
            $attriute['config'] = $config;
            $attriute['route'] = $exname;
            $attriute['application'] = $application;
            $application->request($method, $exname, $config['url'] ?? '/', $attriute);
        }
    }

    public function registerRoute(Route $route, Application $application)
    {
        $application->debug()->info('register open-method routes ...');
        foreach ($application->getModule()->all() as $fullName => $module) {
            if ($path = $module->getResource()->getConfigResourcePath('config/open-method')) {
                $routeConfig = Config::loadConfig($path, [
                    'module' => $fullName,
                    'config' => $module->getConfig(),
                ]);
                if ($routeConfig !== null) {
                    $this->prepareRoute($application, $fullName, $module, $routeConfig);
                }
            }
        }
    }
}
