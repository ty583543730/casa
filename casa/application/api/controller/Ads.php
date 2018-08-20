<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/4 0004
 * Time: 下午 4:06
 */

namespace app\api\controller;

use app\api\model\Ads as A;
class Ads
{
    /** 广告列表
     * @return mixed
     */
    public function lists ($param) {
        $object = new A();
        $result = $object->lists($param);
        return $result;
    }
}