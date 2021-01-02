<?php


namespace xuezhitech\wxpay\V3;


class Transactions
{
    private $transactions_url = 'https://api.mch.weixin.qq.com/v3/combine-transactions/jsapi';

    private $refunds_url = 'https://api.mch.weixin.qq.com/v3/ecommerce/refunds/apply';

    private $config = [];

    private $sign = null;

    private $curl = null;

    private $certificates = null;

    public function __construct($config=[]){
        $this->config = array_merge($this->config,$config);
        $this->sign = new \xuezhitech\wxpay\Util\Sign($this->config);
        $this->curl = new \xuezhitech\wxpay\Util\Curl();
        $this->certificates = new \xuezhitech\wxpay\V3\Certificates($config);
    }

    //退款申请API
    public function refunds( $data ) {
        //微信平台私钥
        $serial_no = $this->certificates->getWechatCertificates();
        $body = json_encode($data,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        //获得签名
        $token = $this->sign->getSign($this->refunds_url,'POST',$body);
        //拼头陪信息
        $header = [
            'User-Agent:' . $this->config['user_agent'],
            'Accept:application/json;charset=utf-8',
            'Content-Type:application/json',
            'Wechatpay-Serial: ' .  $serial_no,
            'Authorization: ' . $this->config['schema'] . ' ' . $token
        ];
        return $this->curl->getInfo($this->refunds_url,'POST',$body,$header);
    }

    //合单下单-JSAPI支付/小程序支付API
    public function combineTransactions( $data ) {
        //微信平台私钥
        $serial_no = $this->certificates->getWechatCertificates();
        $body = json_encode($data,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        //获得签名
        $token = $this->sign->getSign($this->transactions_url,'POST',$body);
        //拼头陪信息
        $header = [
            'User-Agent:' . $this->config['user_agent'],
            'Accept:application/json;charset=utf-8',
            'Content-Type:application/json',
            'Wechatpay-Serial: ' .  $serial_no,
            'Authorization: ' . $this->config['schema'] . ' ' . $token
        ];
        return $this->curl->getInfo($this->transactions_url,'POST',$body,$header);
    }
}
