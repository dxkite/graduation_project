<?php
namespace dxkite\openuser\sender;


use suda\application\Application;
use suda\application\exception\ConfigurationException;
use suda\framework\Config;
use support\smtp\Sender;

class SMTPSender extends Sender
{

    /**
     * @var Application
     */
    protected static $application;

    /**
     * SMTPSender constructor.
     * @param Application $application
     * @return SMTPSender
     */
    public static function build(Application $application)
    {
        static::$application = $application;
        static::$application->getDebug()->addIgnorePath(__FILE__);
        $config = static::getConfig();
        $server = $config['server'];
        $port = $config['port'] ?? 465;
        $timeout = $config['timeout'] ?? 3;
        $name = $config['name'];
        $password = $config['password'];
        $security = $config['security'] ?? true;
        return new self($server, $port, $timeout, $name, $password, $security);
    }

    protected static function getConfig() {
        $data = new \suda\application\Resource(static::$application->getDataPath());
        $path = $data->getConfigResourcePath('config/mailer-smtp');
        if ($path !== null) {
            $config = Config::loadConfig($path);
            if (is_array($config)) {
                return $config;
            }
        }
        throw new ConfigurationException('config @data/config/mailer-smtp not found');
    }

    protected function log(string $message)
    {
        static::$application->getDebug()->info($message);
    }
}