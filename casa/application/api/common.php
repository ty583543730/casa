<?php

use think\Db;
use app\api\model\Login;


/**
 * 获取客户端登录用户userId
 * @param string $app
 * @return int|mixed
 */
function userId()
{
    $uid = 0;
    $userLogin = new Login();
    $res = $userLogin->loginGet();
    if ($res === false) {
        return false;
    }
    if (config("uid") == -100) {
        exception('login-10');
    }
    if (empty(config("uid"))) {
        exception('login-2');
    }
    return config("uid");
}

/**
 * 校验用户是否登录
 * @return int|mixed
 */
function ISLogin()
{
    try {
        $userLogin = new Login();
        $userLogin->loginGet();
        if (config("uid") == -100) {
            exception('login-10');
        }
        if (empty(config("uid"))) {
            exception('login-2');
        }
        userType(config("uid"));
    } catch (Exception $e) {
        return SKReturn($e->getMessage());
    }
}

/**
 * 校验登录用户状态
 * @param string $app
 * @return int|mixed
 */
function userType($uid)
{
    $type = Db::name('users')->where('userId', $uid)->field('userStatus')->find();
    if ($type['userStatus'] != 1) {
        echo JSONReturn(['status' => -11, 'msg' => '账号已经被停用']);
        exit();
    }
}