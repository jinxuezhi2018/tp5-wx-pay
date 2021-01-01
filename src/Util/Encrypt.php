<?php


namespace xuezhitech\wxpay\Util;


class Encrypt
{
    private $wxpay_key_path;

    public function __construct( $wxpay_key_path ){
        $this->wxpay_key_path = $wxpay_key_path;
    }

    public function getEncrypt($data) {
        $sign = false;
        $wxpay_key = file_get_contents($this->wxpay_key_path);
        if (\openssl_public_encrypt($data,$encrypted,$wxpay_key,OPENSSL_PKCS1_OAEP_PADDING)) {
            $sign = base64_encode($encrypted);
        } else {
            $sign = false;
        }
        return $sign;
    }
}
