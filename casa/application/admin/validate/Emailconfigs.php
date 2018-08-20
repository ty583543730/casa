<?php

namespace app\admin\validate;

use think\Validate;

/**
 * 权限验证器
 */
class Emailconfigs extends Validate
{
    protected $rule = [
        ['mailSmtp', 'require|max:64', '请输入SMTP服务器|SMTP服务器不能超过64个字节'],
        ['mailPort', 'require|number|max:5', '请输入SMTP端口|SMTP端口必须为数字|SMTP端口必须小于等于5个字节'],
        ['mailAuth', ['require', '/^(true|false)$/i'], '请输入是否验证SMTP|是否验证SMTP只能输入true或false'],
        ['mailAddress', 'require|email', '请输入SMTP发件人邮箱|邮箱格式不正确'],
        ['mailUserName', 'require|max:64', '请输入SMTP登录账号|SMTP登录账号不能超过64个字节'],
        ['mailPassword', 'require|max:64', '请输入SMTP登录密码|SMTP登录密码不能超过64个字节'],
        ['mailSendTitle', 'require|max:60', '请输入发件人标题|发件人标题不能超过20个字符'],
        ['useTime', 'require|number', '请输入今天可使用次数|今天可使用次数必须为整数']
    ];

    protected $scene = [
        'add' => ['mailSmtp', 'mailPort', 'mailAuth', 'mailAddress', 'mailUserName', 'mailPassword', 'mailSendTitle', 'useTime'],
        'edit' => ['mailSmtp', 'mailPort', 'mailAuth', 'mailAddress', 'mailUserName', 'mailPassword', 'mailSendTitle', 'useTime']
    ];
}