<?php


namespace dxkite\openuser\sender;


use Exception;
use Qcloud\Sms\SmsSingleSender;
use suda\application\Application;
use suda\application\exception\ConfigurationException;
use suda\framework\Config;
use support\smtp\FileNoFoundException;
use support\smtp\Message;


class Send
{
    /**
     * 发送邮件
     * @param Application $application
     * @param string $action
     * @param string $mail
     * @param string $code
     * @param string $expireIn
     * @return bool
     */
    public static function mail(Application $application, string $action, string $mail, string $code, string $expireIn)
    {
        $sender = SMTPSender::build($application);
        $message = new Message('邮箱验证', $application->_('${action}：你的邮箱验证码为：${code}, 本次验证在 ${expireIn} 分钟内有效',[
            'action' => $action,
            'code' => $code,
            'expireIn' => $expireIn,
        ]));
        $message->setTo($mail);
        try {
            return $sender->send($message);
        } catch (FileNoFoundException $e) {
            $application->debug()->error($e->getMessage());
            return false;
        }
    }

    /**
     * 发送短信
     * @param Application $application
     * @param string $action
     * @param string $phone
     * @param string $code
     * @param string $expireIn
     * @return bool|string
     */
    public static function sortMessage(Application $application, string $action, string $phone, string $code, string $expireIn)
    {
        $config = self::getConfig($application);
        $params = [$action, $code, $expireIn];
        $appId = $config['app-id'];
        $appKey = $config['app-key'];
        $singleSender = new SmsSingleSender($appId, $appKey);
        $sign = $config['sign'];
        $templateId = $config['template'];
        try {
            return $singleSender->sendWithParam("86", $phone, $templateId, $params, $sign, "", "");
        } catch (Exception $e) {
            $application->debug()->error($e->getMessage());
            return false;
        }
    }

    protected static function getConfig(Application $application)
    {
        $data = new \suda\application\Resource($application->getDataPath());
        $path = $data->getConfigResourcePath('config/tencent-sms');
        if ($path !== null) {
            $config = Config::loadConfig($path);
            if (is_array($config)) {
                return $config;
            }
        }
        throw new ConfigurationException('config @data/config/tencent-sms not found');
    }
}