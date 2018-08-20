<?php
/**
 * 登录
 * User: yt
 * Date: 2018/5/23 0023
 * Time: 上午 9:00
 */

namespace app\admin\controller;

use app\admin\model\Login as M;

class Login extends Base
{
    /*登录页面*/
    public function index()
    {
        return $this->fetch('index');
    }

    /*检查登录*/
    public function checkLogin()
    {
        $login = new M();
        return $login->checkLogin();
    }

    /**
     * 获取验证码
     */
    public function getVerify()
    {
        getVerify();
    }

    /**
     * 退出系统
     */
    public function logout()
    {
        cache('background_menus_'.session('sk_staff.staffId'),null);
        session('sk_staff', null);
        $this->redirect('index');
    }

}