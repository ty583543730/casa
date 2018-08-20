<?php
/**
 * 首页
 * User: yt
 * Date: 2018/5/22 0022
 * Time: 下午 7:55
 */

namespace app\admin\controller;

use app\admin\model\Menus;
use app\admin\model\Count as C;

class Index extends Base
{
    /*主页面*/
    public function index()
    {
        $menus = new Menus();
        $ms = $menus->getMenus();
        $this->assign("menus", $ms);
        return $this->fetch('index');
    }

    /*首页的iframe*/
    public function console()
    {
        $c = new C();
        $data = $c->getCount();
        $this->assign('data', $data);
        return $this->fetch('console');
    }

    /*获取首页图标注册数据*/
    public function registerCount(){
        $c = new C();
        return $c->registerCount();
    }

    /*上传图片*/
    public function uploadPic()
    {
        $dir = input('post.dir');
        return uploadPic($dir);
    }
}
