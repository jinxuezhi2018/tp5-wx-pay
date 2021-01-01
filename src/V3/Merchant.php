<?php


namespace xuezhitech\wxpay\V3;


class Merchant
{
    private $applyments_url = 'https://api.mch.weixin.qq.com/v3/ecommerce/applyments/';

    private $config = [];

    private $sign = null;

    private $curl = null;

    private $certificates = null;

    public function __construct($config=[]){
        $this->config = array_merge($this->config,$config);
        $this->sign = new \xuezhitech\wx\Util\Sign($this->config);
        $this->curl = new \xuezhitech\wx\Util\Curl();
        $this->certificates = new \xuezhitech\wx\V3\Certificates($config);
    }
    //查询申请状态API
    public function applymentStatus( $applyment_id ) {
        $url = $this->applyments_url . $applyment_id;
        $body = '';
        //获得签名
        $token = $this->sign->getSign($url,'GET',$body);
        //拼头陪信息
        $header = [
            'User-Agent:' . $this->config['user_agent'],
            'Accept:application/json;charset=utf-8',
            'Content-Type:application/json',
            'Authorization: ' . $this->config['schema'] . ' ' . $token
        ];
        return $this->curl->getInfo($url,'GET',$body,$header);
    }
    //二级商户进件API
    public function applyments( $data ) {
        //微信平台私钥
        $serial_no = $this->certificates->getWechatCertificates();
        $body = json_encode($data,JSON_UNESCAPED_UNICODE);
        //获得签名
        $token = $this->sign->getSign($this->applyments_url,'POST',$body);
        //拼头陪信息
        $header = [
            'User-Agent:' . $this->config['user_agent'],
            'Accept:application/json;charset=utf-8',
            'Content-Type:application/json',
            'Wechatpay-Serial: ' .  $serial_no,
            'Authorization: ' . $this->config['schema'] . ' ' . $token
        ];
        return $this->curl->getInfo($this->applyments_url,'POST',$body,$header);
    }
}
