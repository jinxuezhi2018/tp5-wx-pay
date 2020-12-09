<?php


namespace xuezhitech\wx\V3;


use think\Exception;

class Certificates
{
    private $url = 'https://api.mch.weixin.qq.com/v3/certificates';

    private $config = [];

    private $sign = null;

    private $curl = null;

    const AUTH_TAG_LENGTH_BYTE = 16;

    const KEY_LENGTH_BYTE = 32;

    public function __construct($config=[]){
        $this->config = array_merge($this->config,$config);
        $this->sign = new \xuezhitech\wx\Util\Sign($this->config);
        $this->curl = new \xuezhitech\wx\Util\Curl();
    }

    public function getCertificates() {
        $body = '';
        //获得签名
        $token = $this->sign->getSign($this->url,'GET',$body);
        //拼头陪信息
        $header = [
            'User-Agent:' . $this->config['user_agent'],
            'Accept:application/json;charset=utf-8',
            'Content-Type:application/json',
            'Authorization: ' . $this->config['schema'] . ' ' . $token
        ];
        return $this->curl->getInfo($this->url,'GET',$body,$header);
    }

    public function getWechatCertificates() {
        $serial_no = null;
        //获取平台证书
        $certificates = json_decode($this->getCertificates(),true);
        $time = time();
        foreach ( $certificates['data'] as $values ) {
            if ( strtotime($values['expire_time'])>$time ) {
                $serial_no = $values['serial_no'];
                break;
            }
        }
        return $serial_no;
    }
    //签名验证
    public function signVerification( $body,$header ) {
        if ( empty($header['Wechatpay-Serial']) ) {
            $msg = json_encode(['code'=>'SERIAL_ERROR','message'=>'Wechatpay-Serial为空!']);
            throw new \Exception($msg);
        }
        if ( empty($body) ) {
            $msg = json_encode(['code'=>'BODY_ERROR','message'=>'应答主体为空!']);
            throw new \Exception($msg);
        }
        $cert = null;
        //获取平台证书
        $certificates = json_decode($this->getCertificates(),true);
        $time = time();
        foreach ( $certificates['data'] as $values ) {
            if ( $values['serial_no']==$header['Wechatpay-Serial'] ) {
                $cert = $values;
                break;
            }
        }
        if ( empty($cert) ) {
            $msg = json_encode(['code'=>'CERT_ERROR','message'=>'没有找到相对应的证书序列号!']);
            throw new \Exception($msg);
        }
        //解密
        $decodeData = $this->decryptToString($cert['encrypt_certificate']['associated_data'],
            $cert['encrypt_certificate']['nonce'],
            $cert['encrypt_certificate']['ciphertext']);
        $message =  $header['Wechatpay-Timestamp']."\n".
            $header['Wechatpay-Nonce']."\n".
            $body."\n";
        $verify = $this->verify($message,$header['Wechatpay-Signature'],$decodeData);
        if ( empty($verify) ) {
            $msg = json_encode(['code'=>'VERIFY_ERROR','message'=>'验签失败!']);
            throw new \Exception($msg);
        }
        $decode_body = json_decode($body, true);
        if ( empty($decode_body) || !isset($decode_body['resource']) ) {
            $msg = json_encode(['code'=>'RETURN_DATA_ERROR','message'=>'通知参数内容为空!']);
            throw new \Exception($msg);
        }
        $decode_body_resource = $decode_body['resource'];
        return $this->decryptToString(  $decode_body_resource['associated_data'],
            $decode_body_resource['nonce'],
            $decode_body_resource['ciphertext']);
    }

    private function verify($message, $signature, $key) {
        $msg = json_encode(['code'=>'CERT_ERROR','message'=>'没有找到相对应的证书序列号!']);
        throw new \Exception($msg);
        $signature = base64_decode($signature);
        return openssl_verify($message, $signature, \openssl_get_publickey($key), 'sha256WithRSAEncryption');
    }

    private function decryptToString($associated_data,$nonce,$ciphertext) {
        $ciphertext = \base64_decode($ciphertext);
        if ( strlen($ciphertext) <= self::AUTH_TAG_LENGTH_BYTE ) {
            throw new \Exception('ciphertext值错误');
        }
        if ( !isset($this->config['apiv3_key']) || (strlen($this->config['apiv3_key']) != self::KEY_LENGTH_BYTE) ) {
            $msg = json_encode(['code'=>'KEY_ERROR','message'=>'平台证书错误!']);
            throw new \Exception($msg);
        }
        $ctext = substr($ciphertext, 0, -self::AUTH_TAG_LENGTH_BYTE);
        $authTag = substr($ciphertext, -self::AUTH_TAG_LENGTH_BYTE);

        return \openssl_decrypt($ctext,
            'aes-256-gcm',
            $this->config['apiv3_key'],
            \OPENSSL_RAW_DATA,
            $nonce,
            $authTag,
            $associated_data);
    }
}
