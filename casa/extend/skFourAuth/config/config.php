<?php

class Config
{
    //正式资料
    private $cfg = array(
        "url" => "https://auth-api.daliuliang.com.cn/",
        "appId" => "66004",             //商户号
        "key" => "qvfH4qkHg6YfcF4V",   //平台KEY值
        "version" => "2.0"
    );

    public function C($cfgName)
    {
        return $this->cfg[$cfgName];
    }

}

?>