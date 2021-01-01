<?php


namespace xuezhitech\wxpay\V3;


class Upload
{
    private $url = 'https://api.mch.weixin.qq.com/v3/merchant/media/upload';

    private $config = [];

    private $sign = null;

    private $curl = null;

    public function __construct($config=[]){
        $this->config = array_merge($this->config,$config);
        $this->sign = new \xuezhitech\wx\Util\Sign($this->config);
        $this->curl = new \xuezhitech\wx\Util\Curl();
    }

    public function imgUpload( $filePath ) {
        $mime_type = $this->getMimeType($filePath);
        $filename = basename($filePath);
        //body 分割符
        $boundary = md5(uniqid());
        //拼meta
        $meta['filename'] = $filename;
        $meta['sha256'] = hash_file('sha256',$filePath);
        //获得签名
        $token = $this->sign->getSign($this->url,'POST',json_encode($meta));
        //拼头陪信息
        $header = [
            'User-Agent:' . $this->config['user_agent'],
            'Accept:application/json;charset=utf-8',
            'Content-Type:multipart/form-data;boundary=' . $boundary,
            'Authorization: ' .$this->config['schema'] . ' ' . $token
        ];
        //拼body
        $boundary_str = "--{$boundary}\r\n";
        $body = $boundary_str;
        $body .= 'Content-Disposition: form-data; name="meta";'."\r\n";
        $body .= 'Content-Type: application/json'."\r\n";
        $body .= "\r\n";
        $body .= json_encode($meta) . "\r\n";
        $body .= $boundary_str;
        $body .= 'Content-Disposition: form-data; name="file"; filename="'.$filename.'";'."\r\n";
        $body .= 'Content-Type: ' . $mime_type . "\r\n";
        $body .= "\r\n";
        $body .= file_get_contents($filePath)."\r\n";
        $body .= "--{$boundary}--\r\n";

        return $this->curl->getInfo($this->url,'POST',$body,$header);
    }

    private function getMimeType( $file ){
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        return finfo_file($finfo, $file);
    }
}
