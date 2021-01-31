<?php


namespace xuezhitech\wxpay\V3;


class Fund
{
    private $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/fund';

    private $config = [];

    private $sign = null;

    private $curl = null;

    public function __construct($config=[]){
        $this->config = array_merge($this->config,$config);
        $this->sign = new \xuezhitech\wxpay\Util\Sign($this->config);
        $this->curl = new \xuezhitech\wxpay\Util\Curl();
    }

    //二级商户余额提现API
    public function withdraw( $data ){
        $url = $this->url . '/withdraw';
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

    //查询分账结果API
    public function searchBalance( $sub_mchid ){
        if ( empty($sub_mchid) ) {
            $msg = json_encode(['code'=>'ORDER_ERROR','message'=>'二级商户号为空!']);
            throw new \Exception($msg);
        }
        $url = $this->url . '/balance/'.$sub_mchid;
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
}
