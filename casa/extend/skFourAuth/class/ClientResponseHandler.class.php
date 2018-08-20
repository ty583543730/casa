<?php

/**
 * 后台应答类
 * ============================================================================
 * api说明：
 * getKey()/setKey(),获取/设置密钥
 * getContent() / setContent(), 获取/设置原始内容
 * getParameter()/setParameter(),获取/设置参数值
 * getAllParameters(),获取所有参数
 * isTenpaySign(),是否威富通签名,true:是 false:否
 * getDebugInfo(),获取debug信息
 *
 * ============================================================================
 *
 */

use think\Log;

class ClientResponseHandler
{

    /** 密钥 */
    var $key;

    /** 应答的参数 */
    var $parameters;

    /** debug信息 */
    var $debugInfo;

    //原始内容
    var $content;

    function __construct()
    {
        $this->ClientResponseHandler();
    }

    function ClientResponseHandler()
    {
        $this->key = "";
        $this->parameters = array();
        $this->debugInfo = "";
        $this->content = "";
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

    //设置原始内容
    function setContent($content, $desIv)
    {
        $arrayData = json_decode($content, true);
        if (is_array($arrayData)) {
            if (!empty($arrayData['contents'])) { //调用成功
                //解密
                $lastDate = $this->decrypt($arrayData['contents'], $desIv);
                return json_decode($lastDate, true);
            } elseif (!empty($arrayData['msg'])) { //调用失败
                return $arrayData;
            }
        } else {
            return "检查api地址或网络";
        }
    }

    function decrypt($encrypted, $desIv)
    {
        $encrypted = base64_decode($encrypted);
        $key = str_pad($this->key, 24, '0');
        $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_CBC, '');
        if ($desIv == '') {
            $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        } else {
            $iv = $desIv;
        }
        $ks = mcrypt_enc_get_key_size($td);
        @mcrypt_generic_init($td, $key, $iv);
        $decrypted = mdecrypt_generic($td, $encrypted);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $y = $this->pkcs5_unpad($decrypted);
        return $y;
    }

    function pkcs5_unpad($text)
    {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }

    //获取原始内容
    function getContent()
    {
        return $this->content;
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
     *获取所有请求的参数
     * @return array
     */
    function getAllParameters()
    {
        return $this->parameters;
    }

}


?>