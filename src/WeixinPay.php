<?php

namespace xuezhitech\wx;

use think\facade\Config;
use think\helper\Str;

class WeixinPay
{
    private $config = [
        'app_id'=>'',
        'mch_id'=>'',
        'key'=>'',
        'trade_type'=>'',
    ];

    public function __construct($config=[]){
        $this->config = array_merge($this->config,$config);
    }

    /**
     * 统一下单
     * 返回qc_code
     */
    public function unifiedorder($notifyUrl,$outTradeNo,$totalFee,$trade_type,$body){
        $params = [
            'appid' => $this->config['app_id'],
            'mch_id'=> $this->config['mch_id'],
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
            'notify_url'=> $notifyUrl,
            'nonce_str' => Str::random(32),
            'out_trade_no' => $outTradeNo,
            'total_fee'=> $totalFee*100,
            'trade_type'=> $trade_type,
            'body' => $body,
        ];
        $params['sign'] = $this->sign($params);
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $reslut = $this->getCurlInfo($url,$this->arrayToXml($params));

        return $this->xmlToObject($reslut);
    }

    public function sign($params){
        ksort($params);
        $paramsStr = '';
        foreach ($params as $key => $val) {
            $paramsStr .= "{$key}={$val}&";
        }
        $sign = $paramsStr . 'key=' . $this->key;
        return Str::upper(md5($sign));
    }

    private function getCurlInfo($url,$data){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        if ( !empty($data) ) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    private function xmlToObject($xml){
        $obj = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)));
        return $obj;
    }

    private function arrayToXml($arr){
        $xml = "<xml>";
        foreach ($arr as $key=>$val) {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }
}
