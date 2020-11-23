<?php


namespace xuezhitech\wx\Util;


class Curl
{
    private $ch;

    public function __construct(){
        $this->ch = curl_init();
    }

    public function getInfo($url,$type='GET',$data=[],$headers=[]){
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->ch, CURLOPT_SSLVERSION, false);
        curl_setopt($this->ch, CURLOPT_HEADER, FALSE);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
        if ( $headers ) {
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        }
        if ( $type=='POST' ){
            curl_setopt($this->ch, CURLOPT_POST, true);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        }
        $response = curl_exec($this->ch);
        curl_close($this->ch);
        return $response;
    }
}
