<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/11 0011
 * Time: 下午 4:40
 */

namespace app\api\validate;
;

use think\Db;
use think\Validate;

class Transfer extends Validate
{
    protected $rule = [
        ['userPhone',"require|verifyPhone","请输入手机号码|手机号码出错"],
        ['num', ['require', '/^[0-9]+(.[0-9]{1,4})?$/', 'verifyNum'], '请输入币的数量|数量最多只能为小数点四位|币不足'],
    ];

    /**
     * @param $value    验证数据
     * @param $rule     验证规则
     * @param $data     全部数据（数组）
     * @param $field    字段名
     */
    protected function verifyPhone($value, $rule, $data, $field)
    {
        if (!SKIsPhone($value)) {
            return "手机号码格式不对";
        }
        $user = Db::name("users")->field("userPhone")->where(['userId' => $data['userId']])->find();
        if ($user['userPhone'] == $value) {
            return "请输入他人的手机号码";
        }
        $user = Db::name("users")->field("userStatus")->where(['userPhone' => $value])->find();
        if (empty($user)) {
            return "该手机号码未注册";
        }
        if ($user['userStatus'] == -1) {
            return "该手机已经被禁止使用";
        }
        return true;
    }

    /**
     * @param $value    验证数据
     * @param $rule     验证规则
     * @param $data     全部数据（数组）
     * @param $field    字段名
     */
    protected function verifyNum($value, $rule, $data, $field)
    {
        $userCoin = Db::name("users_coin")->field("black,locker")->where(['userId' => $data['userId'],'coin' => 'CASA'])->find();
        if ($userCoin['black'] < $value) {
            return "可用币数量不足";
        }
        return true;
    }

    protected $scene = [
        'userPhone' => ['userPhone'],
        'num' => ['num'],
        'transfer' => ['userPhone','num'],
    ];
}