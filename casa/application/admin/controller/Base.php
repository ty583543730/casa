<?php
/**
 * 基类
 * User: yt
 * Date: 2018/5/22 0022
 * Time: 下午 7:55
 */

namespace app\admin\controller;


use think\Controller;

class Base extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->assign("v", time());
    }

}