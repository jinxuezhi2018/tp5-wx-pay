<?php


namespace xuezhitech\wx\V3;


class Certificates
{
    private $url = 'https://api.mch.weixin.qq.com/v3/certificates';

    private $config = [];

    private $sign = null;

    private $curl = null;

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

}
