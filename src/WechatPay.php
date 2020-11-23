<?php

namespace xuezhitech\wx;
namespace xuezhitech\wx\Util;


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
    public function upload() {
        $upload = new \xuezhitech\wx\Util\Upload($this->config);
        return $upload->imgUpload();
    }


}
