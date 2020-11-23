<?php

namespace xuezhitech\wx;

class WechatPay
{
    private $config = [
        'merchant_id' => '',
        'serial_no' => '',
        'private_key' => '',
        'user_agent' => '',
        'schema'=>'WECHATPAY2-SHA256-RSA2048'
    ];

    public function __construct($config=[]){
        $this->config = array_merge($this->config,$config);
    }

    //图片上传
    public function upload( $file ) {
        $upload = new \xuezhitech\wx\V3\Upload($this->config);
        return $upload->imgUpload( $file );
    }


}
