<?php

namespace app\admin\validate;

use think\Validate;
use think\Db;

/**
 * 用户验证器
 */
class Users extends Validate
{
    protected $rule = [
        ['userPhone', 'require|max:11|checkPhone:1', '请输入登录账号|手机号码错误'],
        ['userName', 'require|max:2', '请输入登录账号|登录账号不能超过20个字符'],
        ['loginPwd', 'require|min:6', '请输入登录密码|登录密码不能少于6个字符'],
        ['payPwd', 'require|min:6', '请输入登录密码|登录密码不能少于6个字符'],
        ['proxyId', 'require|in:1,2,3', '请输入职员名称|无效的用户类型值'],
        ['proxyStatus', 'require|in:-1,1', '请选择工作状态|无效的状态值'],
    ];

    protected $scene = [
        'add' => ['userPhone', 'loginName', 'loginPwd','payPwd'],
        'edit' => ['userName', 'proxyId']
    ];

    protected function checkPhone($value)
    {
        $where = [];
        $where['dataFlag'] = 1;
        $where['userPhone'] = $value;
        $rs = Db::name('users')->where($where)->count();
        return ($rs == 0) ? true : '该手机已注册已存在';
    }
}