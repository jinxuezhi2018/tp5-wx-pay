<?php

namespace xuezhitech\wxpay;

use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;

class WechatPay
{
    protected static $instance;
    /*
    private $config = [
        'merchant_id' => '',
        'serial_no' => '',
        'private_key' => '',
        'wxpay_key' => '',
        'user_agent' => '',
        'apiv3_key' => '',
        'schema'=>'WECHATPAY2-SHA256-RSA2048'
    ]; */
    protected function bindParams($reflect, $vars = []){
        if ($reflect->getNumberOfParameters() == 0) {
            return [];
        }
        // 判断数组类型 数字数组时按顺序绑定参数
        reset($vars);
        $type   = key($vars) === 0 ? 1 : 0;
        $params = $reflect->getParameters();

        foreach ($params as $param) {
            $name      = $param->getName();
            //$lowerName = Loader::parseName($name);
            //$class     = $param->getClass();
            var_dump($name);exit;

            if ($class) {
                $args[] = $this->getObjectParam($class->getName(), $vars);
            } elseif (1 == $type && !empty($vars)) {
                $args[] = array_shift($vars);
            } elseif (0 == $type && isset($vars[$name])) {
                $args[] = $vars[$name];
            } elseif (0 == $type && isset($vars[$lowerName])) {
                $args[] = $vars[$lowerName];
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } else {
                throw new InvalidArgumentException('method param miss:' . $name);
            }
        }

        return $args;
    }

    public function invokeClass($class, $vars = []){
        try {
            $reflect = new ReflectionClass($class);
            $constructor = $reflect->getConstructor();
            $args = $constructor ? $this->bindParams($constructor, $vars) : [];
            return $reflect->newInstanceArgs($args);
        } catch (ReflectionException $e) {
            throw new ReflectionException($e);
        }
    }

    public function make($abstract, $vars = []){
        $abstract = isset($this->name[$abstract]) ? $this->name[$abstract] : $abstract;
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }
        $object = $this->invokeClass($abstract, $vars);
        return $object;
    }

    public static function get($abstract, $vars = []){
        return static::getInstance()->make($abstract, $vars);
    }

    public static function getInstance(){
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    /*

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
        return $transactions->combineTransactions( $body );
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

    //添加分账接收方API
    public function receiversAdd( $body ){
        $profitsharing = new \xuezhitech\wx\V3\Profitsharing( $this->config );
        return $profitsharing->receiversAdd( $body );
    }

    //删除分账接收方API
    public function receiversDel( $body ) {
        $profitsharing = new \xuezhitech\wx\V3\Profitsharing( $this->config );
        return $profitsharing->receiversDel( $body );
    }

    //请求分账API
    public function orders( $body ) {
        $profitsharing = new \xuezhitech\wx\V3\Profitsharing( $this->config );
        return $profitsharing->orders( $body );
    }

    //请求分账API
    public function create( $body ) {
        $subsidies = new \xuezhitech\wx\V3\Subsidies( $this->config );
        return $subsidies->create( $body );
    }

    //查询订单剩余待分金额API
    public function ordersAmounts( $transaction_id ) {
        $profitsharing = new \xuezhitech\wx\V3\Profitsharing( $this->config );
        return $profitsharing->ordersAmounts( $transaction_id );
    }
    */
}
