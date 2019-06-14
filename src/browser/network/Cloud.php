<?php
namespace qpf\browser\network;

use qpf;
use qpf\eapi\MapResolve;

/**
 * 云服务
 */
class Cloud
{
    /**
     * 发送短信
     * @param array $data 发送的post参数 `key => $value`
     */
    public function sms(array $data) {
        $curl = new Curl();
        $eapi = new MapResolve();
        $eapi = $eapi->parseApiMap([
            'testSMS'  => [
                'url'   => 'http://qpf3.com',
                'param' => [
                    'secret'    => [
                        'set'   => true,
                        'default'   => '',
                    ], 
                    'uid'       => [
                        'set'   => true,
                        'default'   => '',
                    ],
                    'm'         => 'store',
                    'action'    => 'controller',
                ],
            ],
        ]);
        
        $url = $eapi['testSMS']->getApi(['secret' => 'utf8', 'uid' => '123456']);
        echor_exit($url);
        $result = $curl->post($url, $data);
        
        return json_decode($result, true);
    }
}