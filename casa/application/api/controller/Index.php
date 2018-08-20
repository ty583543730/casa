<?php
/**
 * 首页数据
 * User: yt
 * Date: 2018/8/13
 * Time: 10:51
 */

namespace app\api\controller;

use app\api\model\Index as M;

class Index extends Base
{
    /*未登录的数据图片，公告数据*/
    public function index()
    {
        $m= new M();
        return $m->index();
    }
}