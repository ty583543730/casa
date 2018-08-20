<?php
/**
 * 短信模版 使用接口：fastoo.
 * User: yt
 * Date: 2018/6/19 0019
 * Time: 下午 6:04
 */

namespace sms\fastoo;


class template
{
    private $TemplateCode = [
        "login" => "您的验证码是 %s，请勿泄露！", //登录
        "findLoginPwd" => "您的验证码是 %s，请勿泄露！", //找回登录密码
        "findPayPwd" => "您的验证码是 %s，请勿泄露！",    //找回交易密码
        "register" => "您的验证码是 %s，请勿泄露！",  //注册
        "draw" => "您的验证码是 %s，请勿泄露！",  //提现
        "bindingMobile" => "您的验证码是 %s，请勿泄露！", //手机绑定
        "unBindMobile" => "您的验证码是 %s，请勿泄露！",  //手机解绑
        "bindingEmail" => "您的验证码是 %s，请勿泄露！", //手机绑定
        "unBindEmail" => "您的验证码是 %s，请勿泄露！",  //手机解绑
        "bindAccount" => "您的验证码是 %s，请勿泄露！",  //支付系统绑定交易所账号
    ];
    private $TemplateCode_En = [
        "login" => "您的验证码是 %s，请勿泄露！", //登录
        "findLoginPwd" => "您的验证码是 %s，请勿泄露！", //找回登录密码
        "findPayPwd" => "您的验证码是 %s，请勿泄露！",    //找回交易密码
        "register" => "您的验证码是 %s，请勿泄露！",  //注册
        "draw" => "您的验证码是 %s，请勿泄露！",  //提现
        "bindingMobile" => "您的验证码是 %s，请勿泄露！", //手机绑定
        "unBindMobile" => "您的验证码是 %s，请勿泄露！",  //手机解绑
        "bindingEmail" => "您的验证码是 %s，请勿泄露！", //手机绑定
        "unBindEmail" => "您的验证码是 %s，请勿泄露！",  //手机解绑
        "bindAccount" => "您的验证码是 %s，请勿泄露！",  //支付系统绑定交易所账号
    ];

    public function sprintf($function, $code, $userName = '', $En = false)
    {
        if ($En) {
            $string = sprintf($this->TemplateCode_En[$function], $code);
        } else {
            $string = sprintf($this->TemplateCode[$function], $code);
        }
        return $string;
    }
}