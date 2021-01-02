<?php

namespace xuezhitech\wxpay;


use ReflectionClass;
use ReflectionException;


class WechatPay
{
    private $namespace = 'xuezhitech\wxpay\V3';

    protected static $instance;

    public function bindParams($reflect, $vars = []){
        if ($reflect->getNumberOfParameters() == 0) {
            return [];
        }
        return [$vars];
    }

    public function invokeClass($class, $vars = []){
        try {
            $reflect = new ReflectionClass($this->namespace . '\\' . $class);
            $constructor = $reflect->getConstructor();
            if ( $constructor ) {
                $args = $this->bindParams($constructor, $vars);
            }else{
                $args = [];
            }
            return $reflect->newInstanceArgs($args);
        } catch (ReflectionException $e) {
            throw new ReflectionException($e);
        }
    }

    public function make($abstract, $vars = []){
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
}
