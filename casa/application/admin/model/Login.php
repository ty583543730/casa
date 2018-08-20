<?php
/**
 * 登录
 * User: yt
 * Date: 2018/5/23 0023
 * Time: 上午 9:29
 */

namespace app\admin\model;


use think\Db;

class Login extends Base
{
    /**
     * 判断用户登录帐号密码
     */
    public function checkLogin()
    {
        $loginName = input("post.loginName");
        $loginPwd = input("post.loginPwd");
        $code = input("post.verifyCode");
        if (!checkVerify($code)) {
            return SKReturn('验证码错误!');
        }
        $staff = Db::name('staffs')->where(['loginName' => $loginName, 'workStatus' => 1, 'dataFlag' => 1])->find();
        if (empty($staff)) return SKReturn('账号或密码错误!');
        if ($staff['loginPwd'] == md5($loginPwd . $staff['secretKey'])) {
            $staff_update = [
                'staffId' => $staff['staffId'],
                'lastTime' => date('Y-m-d H:i:s'),
                'lastIP' => request()->ip()
            ];
            Db::name('staffs')->update($staff_update);
            //记录登录日志
            Db::name('log_staff_logins')->insert([
                'staffId' => $staff['staffId'],
                'loginTime' => date('Y-m-d H:i:s'),
                'loginIp' => request()->ip()
            ]);
            if ($staff['staffId'] == 1) {
                $staff['privileges'] = Db::name('privileges')->where(['dataFlag' => 1])->column('privilegeCode');
                $staff['menuIds'] = Db::name('menus')->where(['dataFlag' => 1])->column('menuId');
            } else {
                //获取角色权限
                $role = Db::name('roles')->where(['dataFlag' => 1, 'roleId' => $staff['staffRoleId']])->find();
                $staff['roleName'] = $role['roleName'];
                $staff['privileges'] = explode(',', $role['privileges']);
                $staff['menuIds'] = [];
                //获取管理员拥有的菜单
                if (!empty($staff['privileges'])) {
                    $menus = Db::name('menus')->alias('m')
                        ->join('privileges p', 'm.menuId=p.menuId and p.dataFlag=1', 'inner')
                        ->where(['p.privilegeCode' => ['in', $staff['privileges']], 'm.dataFlag' => 1])
                        ->field('m.menuId')
                        ->select();
                    $menuIds = [];
                    if (!empty($menus)) {
                        foreach ($menus as $key => $v) {
                            $menuIds[] = $v['menuId'];
                        }
                        $staff['menuIds'] = $menuIds;
                    }
                }
            }
            session('sk_staff', $staff);
            return SKReturn("登录成功", 1, $staff);
        }
        return SKReturn('账号或密码错误!');
    }
}