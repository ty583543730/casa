<?php
namespace app\api\model;

use think\Model;
use think\Db;

/**
 * 基础模型器
 */
class Base extends Model
{
    /**
     * 获取空模型
     */
    public function getEModel($tables)
    {
        $rs = Db::query('show columns FROM `' . config('database.prefix') . $tables . "`");
        $obj = [];
        if ($rs) {
            foreach ($rs as $key => $v) {
                $obj[$v['Field']] = $v['Default'];
                if ($v['Key'] == 'PRI') $obj[$v['Field']] = 0;
            }
        }
        return $obj;
    }

    /**
     * 校验帐号状态
     * @return boolean
     */
    public function isActive($uid)
    {
        $userStatus = Db::name('users')->where(['userId' => $uid])->value('userStatus');
        if ($userStatus == 0) {
            return SKReturn("你的账号被停用", -1);
        }
        return SKReturn("正常", 1);
    }

}