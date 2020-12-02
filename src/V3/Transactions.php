<?php


namespace xuezhitech\wx\V3;


class Transactions
{
    private $url = 'https://api.mch.weixin.qq.com/v3/combine-transactions/jsapi';

    private $config = [];

    private $sign = null;

    private $curl = null;

    public function __construct($config=[]){
        $this->config = array_merge($this->config,$config);
        $this->sign = new \xuezhitech\wx\Util\Sign($this->config);
        $this->curl = new \xuezhitech\wx\Util\Curl();
    }

    //合单下单-JSAPI支付/小程序支付API
    public function combineTransactions( $data ) {
        $body = json_encode($data,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        //获得签名
        $token = $this->sign->getSign($this->url,'POST',$body);
        //拼头陪信息
        $header = [
            'User-Agent:' . $this->config['user_agent'],
            'Accept:application/json;charset=utf-8',
            'Content-Type:application/json',
            'Authorization: ' . $this->config['schema'] . ' ' . $token
        ];
        return $this->curl->getInfo($this->url,'POST',$body,$header);
    }

}
