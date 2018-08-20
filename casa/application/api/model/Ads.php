<?php
/**
 * 广告位
 * User: Administrator
 * Date: 2018/6/4 0004
 * Time: 下午 4:07
 */

namespace app\api\model;


use think\Db;

class Ads
{
    /*
     * 广告列表
     */
    public function lists($param)
    {
        $list = Db::name("ads")
            ->field("adFile,adName,adURL")
            ->where(['dataFlag' => 1, 'adPositionId' => $param['id']])
            ->order("adSort asc")
            ->select();
        return SKReturn("获取成功", 1, $list);
    }
}