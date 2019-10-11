<?php
namespace app\common\library;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

// Download：https://github.com/aliyun/openapi-sdk-php
// Usage：https://github.com/aliyun/openapi-sdk-php/blob/master/README.md

class Alimsg{
    public function __construct()
    {
        AlibabaCloud::accessKeyClient('LTAIdmLHyI7pNRlv', 'CEaTcJ1DDPUr1xSaWUg8s1odPeOYqQ')
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
    }
    public function sendSms($mobile,$code){
        $config = config('AliCloudSms');//读取配置
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host($config['Host'])
                ->options([
                    'query' => [
                        'RegionId' => $config['RegionId'],
                        'PhoneNumbers' => $mobile,
                        'SignName' => $config['SignName'],
                        'TemplateCode' => $config['TemplateCode'],
                        'TemplateParam'=>json_encode(["code"=>$code], JSON_UNESCAPED_UNICODE)
                    ],
                ])
                ->request();
            return $result->toArray();
        } catch (ClientException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        }
    }

}