<?php


namespace xuezhitech\wx\Util;


class Sign
{
    private $config = [
        'merchant_id' => '',
        'serial_no' => '',
        'private_key' => '',
    ];

    public function __construct($config=[]){
        $this->config = array_merge($this->config,$config);
    }

    public function getSign($url,$http_method,$body) {
        $merchant_id = $this->config['merchant_id'];
        $serial_no = $this->config['serial_no'];
        $timestamp = time();
        $nonce = md5(uniqid());
        $private_key = \openssl_get_privatekey(\file_get_contents($this->config['private_key']));
        $url_parts = parse_url($url);
        if ( empty($url_parts['query']) ) {
            $canonical_url = $url_parts['path'];
        }else{
            $canonical_url = $url_parts['path'] . "?${url_parts['query']}";
        }
        $message =  $http_method."\n".
            $canonical_url."\n".
            $timestamp."\n".
            $nonce."\n".
            $body."\n";
        openssl_sign($message, $raw_sign, $private_key, 'sha256WithRSAEncryption');
        $sign = \base64_encode($raw_sign);
        return sprintf('mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
            $merchant_id, $nonce, $timestamp, $serial_no, $sign);
    }
}
