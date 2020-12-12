<?php

namespace xuezhitech\wx;

class WechatPay
{
    private $config = [
        'merchant_id' => '',
        'serial_no' => '',
        'private_key' => '',
        'wxpay_key' => '',
        'user_agent' => '',
        'apiv3_key' => '',
        'schema'=>'WECHATPAY2-SHA256-RSA2048'
    ];

    public function __construct($config=[]){
        $this->config = array_merge($this->config,$config);
    }

    //敏感信息加解密
    public function encrypt( $data ) {
        $encrypt = new \xuezhitech\wx\Util\Encrypt($this->config['wxpay_key']);
        return $encrypt->getEncrypt( $data );
    }

    //图片上传
    public function upload( $file ) {
        $upload = new \xuezhitech\wx\V3\Upload($this->config);
        return $upload->imgUpload( $file );
    }

    //二级商户进件
    public function applyments( $body ) {
        $merchant = new \xuezhitech\wx\V3\Merchant( $this->config );
        return $merchant->applyments( $body );
    }

    //二级商户查询申请状态
    public function applymentStatus( $applyment_id ) {
        $merchant = new \xuezhitech\wx\V3\Merchant( $this->config );
        return $merchant->applymentStatus( $applyment_id );
    }

    //合单下单-JSAPI支付/小程序支付API
    public function combineTransactions( $body ) {
        $transactions = new \xuezhitech\wx\V3\Transactions( $this->config );
        return $transactions->refunds( $body );
    }

    //合单下单-JSAPI支付/小程序支付API
    public function refunds( $body ) {
        $transactions = new \xuezhitech\wx\V3\Transactions( $this->config );
        return $transactions->refunds( $body );
    }

    //合单下单-JSAPI支付/小程序支付API
    public function getMiniPaySign( $appid,$time,$nonceStr,$prepay_id ) {
        $sign = new \xuezhitech\wx\Util\Sign( $this->config );
        return $sign->getMiniPaySign( $appid,$time,$nonceStr,$prepay_id );
    }

    //签名验证
    public function signVerification( $body,$header ) {
        $certificates = new \xuezhitech\wx\V3\Certificates( $this->config );
        return $certificates->signVerification( $body,$header );
    }
}