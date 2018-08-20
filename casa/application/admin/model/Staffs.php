<?php

namespace app\admin\model;

use app\admin\model\LogStaffLogins;
use think\Db;

/**
 * 职员业务处理
 */
class Staffs extends Base
{

    /**
     * 分页
     */
    public function pageQuery()
    {
        $key = input('get.key');
        $where = [];
        $where['s.dataFlag'] = 1;
        if ($key != '') $where['loginName|staffName|staffNo'] = ['like', '%' . $key . '%'];
        return Db::name('staffs')->alias('s')->join('roles r', 's.staffRoleId=r.roleId and r.dataFlag=1', 'left')
            ->where($where)->field('staffId,staffName,loginName,workStatus,staffNo,lastTime,lastIP,roleName')
            ->order('staffId', 'desc')->paginate(input('limit/d'))->toArray();
    }

    /**
     * 删除
     */
    public function del()
    {
        $id = input('post.id/d');
        $data = [];
        $data['dataFlag'] = -1;
        Db::startTrans();
        try {
            $result = $this->update($data, ['staffId' => $id]);
            if (false !== $result) {
                Db::commit();
                return SKReturn("删除成功", 1);
            }
        } catch (\Exception $e) {
            Db::rollback();
            return SKReturn('删除失败', -1);
        }
    }

    /**
     * 获取角色权限
     */
    public function getById($id)
    {
        return $this->get(['dataFlag' => 1, 'staffId' => $id]);
    }

    /**
     * 新增
     */
    public function add()
    {
        $data = input('post.');
        $data['secretKey'] = rand(1000, 9999);
        $data["loginPwd"] = md5(input("post.loginPwd") . $data["secretKey"]);
        $data["staffFlag"] = 1;
        $data["createTime"] = date('Y-m-d H:i:s');
        unset($data['staffId']);
        $result = $this->validate('Staffs.add')->allowField(true)->save($data);
        if (false !== $result) {
            return SKReturn("新增成功", 1);
        } else {
            return SKReturn($this->getError());
        }
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $id = input('post.staffId/d');
        $data = input('post.');
        if (!empty($data['loginPwd'])) {
            $secretKey = $this->where('staffId', $id)->value('secretKey');
            $data['loginPwd'] = md5(input("post.loginPwd") . $secretKey);
        } else {
            unset($data['loginPwd']);
        }
        SKUnset($data, 'staffId,secretKey,dataFlag,createTime,lastTime,lastIP');
        $result = $this->validate('Staffs.edit')->allowField(true)->save($data, ['staffId' => $id]);
        if (false !== $result) {
            return SKReturn("编辑成功", 1);
        } else {
            return SKReturn($this->getError());
        }
    }

    /**
     * 检测账号是否重复
     */
    public function checkLoginKey($key)
    {
        $rs = $this->where(['loginName' => $key, 'dataFlag' => 1])->count();
        return ($rs == 0) ? SKReturn('该账号可用', 1) : SKReturn("对不起，该账号已存在");
    }

    /**
     * 修改自己密码
     */
    public function editMyPass($staffId)
    {
        if (input("post.password") == '') SKReturn("密码不能为空");
        $staff = $this->where('staffId', $staffId)->field('secretKey,loginPwd')->find();
        if (empty($staff)) return SKReturn("修改失败");

        $srcPass = md5(input("post.oldpassword") . $staff["secretKey"]);

        if ($srcPass != $staff['loginPwd']) return SKReturn("原密码错误");
        $staff->loginPwd = md5(input("post.password") . $staff["secretKey"]);
        $result = $staff->save();
        if (false !== $result) {
            session('sk_staff',null);
            return SKReturn("修改成功,请重新登录", 1);
        } else {
            return SKReturn($this->getError(), -1);
        }
    }
}
