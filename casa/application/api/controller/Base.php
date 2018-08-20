<?php
/**
 * Created by PhpStorm.
 * User: Bourne
 * Date: 2017-11-8 10:38
 */

namespace app\api\controller;


class Base extends \think\Controller
{

    public function _empty()
    {
        return ["status" => 0, "msg" => "非法访问"];
    }

    /**
     * 上传图片
     */
    public function uploadPic()
    {
        return uploadPic(2);
    }
    /**
     * 获取空模型
     */
    public function getEModel($tables){
        $rs =  Db::query('show columns FROM `'.config('database.prefix').$tables."`");
        $obj = [];
        if($rs){
            foreach($rs as $key => $v) {
                $obj[$v['Field']] = $v['Default'];
                if($v['Key'] == 'PRI')$obj[$v['Field']] = 0;
            }
        }
        return $obj;
    }
}