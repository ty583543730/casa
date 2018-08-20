<?php
/**
 * 邮件短信发送记录
 * User: sky
 * Date: 2018/6/20
 * Time: 16:08
 */

namespace app\admin\controller;

use app\admin\model\EmailSms as M;

class Emailsms extends Base
{

    /**
     * 列表
     */
    public function index()
    {
        return $this->fetch('index');
    }

    /**
     * 获取分页
     */
    public function pageQuery()
    {
        $m = new M();
        return PQReturn($m->pageQuery());
    }
}