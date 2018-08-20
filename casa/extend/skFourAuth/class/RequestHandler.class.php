<?php

/**
 * 请求类
 * ============================================================================
 * api说明：
 * init(),初始化函数，默认给一些参数赋值，如cmdno,date等。
 * getGateURL()/setGateURL(),获取/设置入口地址,不包含参数值
 * getKey()/setKey(),获取/设置密钥
 * getParameter()/setParameter(),获取/设置参数值
 * getAllParameters(),获取所有参数
 * getRequestURL(),获取带参数的请求URL
 * getDebugInfo(),获取debug信息
 *
 * ============================================================================
 *
 */
class RequestHandler
{

    /** 网关url地址 */
    var $gateUrl;

    /** 密钥 */
    var $key;

    /** 请求的参数 */
    var $parameters;

    /** debug信息 */
    var $debugInfo;

    /** debug信息 */
    var $signature;

    function __construct()
    {
        $this->RequestHandler();
    }

    function RequestHandler()
    {
        $this->gateUrl = "https://mch.one2pay.cn/cloud/cloudplatform/api/trade.html";
        $this->key = "l4mdofLTvHkyONpdlyXBiaTv";
        $this->parameters = array();
        $this->debugInfo = "";
    }

    /**
     *初始化函数。
     */
    function init()
    {
        //nothing to do
    }

    /**
     *获取入口地址,不包含参数值
     */
    function getGateURL()
    {
        return $this->gateUrl;
    }

    /**
     *设置入口地址,不包含参数值
     */
    function setGateURL($gateUrl)
    {
        $this->gateUrl = $gateUrl;
    }

    /**
     *获取密钥
     */
    function getKey()
    {
        return $this->key;
    }

    /**
     *设置密钥
     */
    function setKey($key)
    {
        $this->key = $key;
    }

    /**
     *获取参数值
     */
    function getParameter($parameter)
    {
        return isset($this->parameters[$parameter]) ? $this->parameters[$parameter] : '';
    }

    /**
     *设置参数值
     */
    function setParameter($parameter, $parameterValue)
    {
        $this->parameters[$parameter] = $parameterValue;
    }

    /**
     * 一次性设置参数
     */
    function setReqParams($post, $filterField = null)
    {
        if ($filterField !== null) {
            forEach ($filterField as $k => $v) {
                unset($post[$v]);
            }
        }

        //判断是否存在空值，空值不提交
        forEach ($post as $k => $v) {
            if ($k == "limit_pay") {

            } else {
                if (!isset($v)) {
                    unset($post[$k]);
                }
            }
        }
        $this->parameters = $post;
    }

    /**
     *获取所有请求的参数
     * @return array
     */
    function getAllParameters()
    {
        return $this->parameters;
    }

    /**
     *获取debug信息
     */
    function getDebugInfo()
    {
        return $this->debugInfo;
    }

    /**
     *获得签名字符串
     */
    function getSignature()
    {
        return $this->signature;
    }


    /**
     *设置debug信息
     */
    function _setDebugInfo($debugInfo)
    {
        $this->debugInfo = $debugInfo;
    }

    /**
     *创建身份证验证md5摘要
     */
    function twoCreateSign() {
        $signPars = "";
        $array_data = $this->parameters;
        $signData = [
            "idCard" => $array_data['idCard'],
            "realName" => $array_data['realName'],
            "appId" => $array_data['appId'],
            "time" => $array_data['time'],
            "appKey" => $this->getKey(),
        ];
        foreach($signData as $k => $v) {
            $signPars .= $v;
        }
        $sign = md5($signPars);
        $this->setParameter("key", $sign);
        $this->_setDebugInfo($signPars . " => sign:" . $sign);
    }

    /**
     *创建md5摘要,规则是:按参数名称a-z排序,遇到空值的参数不参加签名。
     */
    function threeCreateSign() {
        $signPars = "";
        $array_data = $this->parameters;
        $signData = [
            "bankCard" => $array_data['bankCard'],
            "realName" => $array_data['realName'],
            "appId" => $array_data['appId'],
            "time" => $array_data['time'],
            "appKey" => $this->getKey(),
            "idCard" => $array_data['idCard'],
        ];
        foreach($signData as $k => $v) {
            $signPars .= $v;
        }
        $sign = md5($signPars);
        $this->setParameter("key", $sign);
        $this->_setDebugInfo($signPars . " => sign:" . $sign);
    }

    /**
     *创建md5摘要,规则是:按参数名称a-z排序,遇到空值的参数不参加签名。
     */
    function createSign() {
        $signPars = "";
        $array_data = $this->parameters;
        $signData = [
            "bankCard" => $array_data['bankCard'],
            "realName" => $array_data['realName'],
            "appId" => $array_data['appId'],
            "time" => $array_data['time'],
            "appKey" => $this->getKey(),
            "idCard" => $array_data['idCard'],
            "mobile" => $array_data['mobile'],
        ];
        foreach($signData as $k => $v) {
            $signPars .= $v;
        }
        $sign = md5($signPars);
        $this->setParameter("key", $sign);
        $this->_setDebugInfo($signPars . " => sign:" . $sign);
    }

}

?>