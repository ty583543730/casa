<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31 0031
 * Time: 下午 4:37
 */

namespace app\api\controller;

use app\api\model\Articles as A;

class Articles extends Base
{
    /**
     * 文章列表
     * @author: zjf 2018/5/31
     * @param $userId 用户Id
     */
    public function articlesList($param) {
        $m = new A();
        $result = $m->articlesList($param);
        return $result;
    }

    /**
     * 文章详情
     * @author: zjf 2018/5/31
     */
    public function info($param)
    {
        $m = new A();
        $result = $m->articlesDetail($param);
        return $result;
    }

    /**
     * 主流币行情列表
     * @author: zhouying 2018/6/12
     */
    public function coinList($param) {
        $m = new A();
        $result = $m->coinList($param);
        return $result;
    }

}