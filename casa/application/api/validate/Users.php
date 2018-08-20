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

class Users extends Validate
{
    protected $rule = [
        ['loginPwd', "require|/^[0-9a-zA-Z_]{6,20}$/|confirm:password_confirm", '请输入登录密码|密码长度为6-20位或者格式不对|密码输入不一致'],
        ['payPwd', "require|/^\d{6}$/|confirm:password_confirm", '请输入交易密码|交易密码为6位的纯数字|密码输入不一致'],
        ['userPhoto', "require|length:1,253", "请上传图片|图片路径长度超出限制"],
        ['userName', "require|length:1,30", "请输入用户昵称|用户昵称长度为1-30"],
        ['trueName', "require|length:1,30", "请输入真实姓名|真实姓名长度为1-30"],
        ['cardID', "require|verifyCardID", "请输入身法证号码|身份证号码有误"],
        ['cardUrl', "require|length:1,100", "请上传身份证正面|身份证正面路径长度超出限制"],
        ['cardBackUrl', "require|length:1,100", "请上传身份证反面|身份证反面路径长度超出限制"],
        ['protocol', "require|=:1", "请选择服务协议|请同意服务协议"],
    ];

    /**
     * @param $value    验证数据
     * @param $rule     验证规则
     * @param $data     全部数据（数组）
     * @param $field    字段名
     */
    protected function verifyCardID($value, $rule, $data, $field)
    {
        //校验身份证填写
        if (!validation_filter_id_card($value)) {
            return "身份证填写不正确";
        }
        //校验身份证号码是否已注册
        $users_realname = Db::name('users_realname')->field("checkStatus")->where(['cardID' => $value,'checkStatus' => ['in','0,1']])->find();
        if (!empty($users_realname)) {
            if ($users_realname['checkStatus'] == 0) {
                return "该身份证号码正在审核中";
            }
            if ($users_realname['checkStatus'] == 1) {
                return "该身份证号码已经审核通过";
            }
        }
        return true;
    }

    protected $scene = [
        'loginPwd' => ['loginPwd', 'password_confirm'],
        'payPwd' => ['payPwd', 'password_confirm'],
        'userPhoto' => ['userPhoto'],
        'userName' => ['userName'],
        'realPortAuth' => ['trueName', 'cardID'],
        'realPersonAuth' => ['trueName', 'cardID', 'cardUrl', 'cardBackUrl'],
        'pcRegister' => ['loginPwd', 'password_confirm', 'protocol'],
    ];
}