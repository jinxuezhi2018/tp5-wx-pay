<?php


namespace xuezhitech\wxpay\V3;


class Profitsharing
{
    private $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/profitsharing';

    private $config = [];

    private $sign = null;

    private $curl = null;

    public function __construct($config=[]){
        $this->config = array_merge($this->config,$config);
        $this->sign = new \xuezhitech\wx\Util\Sign($this->config);
        $this->curl = new \xuezhitech\wx\Util\Curl();
    }

    //删除分账接收方API
    public function receiversDel( $data ) {
        $url = $this->url . '/receivers/delete';
        //微信平台私钥
        $body = json_encode($data,JSON_UNESCAPED_UNICODE);
        //获得签名
        $token = $this->sign->getSign($url,'POST',$body);
        //拼头陪信息
        $header = [
            'User-Agent:' . $this->config['user_agent'],
            'Accept:application/json;charset=utf-8',
            'Content-Type:application/json',
            'Authorization: ' . $this->config['schema'] . ' ' . $token
        ];
        return $this->curl->getInfo($url,'POST',$body,$header);
    }

    //添加分账接收方API
    public function receiversAdd( $data ) {
        $url = $this->url . '/receivers/add';
        //微信json格式化
        $body = json_encode($data,JSON_UNESCAPED_UNICODE);
        //获得签名
        $token = $this->sign->getSign($url,'POST',$body);
        //拼头陪信息
        $header = [
            'User-Agent:' . $this->config['user_agent'],
            'Accept:application/json;charset=utf-8',
            'Content-Type:application/json',
            'Authorization: ' . $this->config['schema'] . ' ' . $token
        ];
        return $this->curl->getInfo($url,'POST',$body,$header);
    }

    //请求分账API
    public function orders( $data ){
        $url = $this->url . '/orders';
        //微信json格式化
        $body = json_encode($data,JSON_UNESCAPED_UNICODE);
        //获得签名
        $token = $this->sign->getSign($url,'POST',$body);
        //拼头陪信息
        $header = [
            'User-Agent:' . $this->config['user_agent'],
            'Accept:application/json;charset=utf-8',
            'Content-Type:application/json',
            'Authorization: ' . $this->config['schema'] . ' ' . $token
        ];
        return $this->curl->getInfo($url,'POST',$body,$header);
    }

    //查询订单剩余待分金额API
    public function ordersAmounts( $transaction_id ){
        $url = $this->url . '/orders/' . $transaction_id . '/amounts';
        //获得签名
        $token = $this->sign->getSign($url,'GET','');
        //拼头陪信息
        $header = [
            'User-Agent:' . $this->config['user_agent'],
            'Accept:application/json;charset=utf-8',
            'Content-Type:application/json',
            'Authorization: ' . $this->config['schema'] . ' ' . $token
        ];
        return $this->curl->getInfo($url,'GET','',$header);
    }

    //请求分账回退API
    public function returnOrders( $data ){
        $url = $this->url . '/returnorders';
        //微信json格式化
        $body = json_encode($data,JSON_UNESCAPED_UNICODE);
        //获得签名
        $token = $this->sign->getSign($url,'POST',$body);
        //拼头陪信息
        $header = [
            'User-Agent:' . $this->config['user_agent'],
            'Accept:application/json;charset=utf-8',
            'Content-Type:application/json',
            'Authorization: ' . $this->config['schema'] . ' ' . $token
        ];
        return $this->curl->getInfo($url,'POST',$body,$header);
    }
}
