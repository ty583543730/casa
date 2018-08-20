<?php

/**
 * 入场数据
 * User: tuyi
 * Date: 2018/8/9
 */

namespace app\admin\model;

use think\Db;
use think\Loader;

/**
 * 用户类型业务处理
 */
class UsersEntrance extends Base
{
    public function getDatas()
    {
        $param = input('get.');
        $where = [];
        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['createTime'] = timeTerm(input('startDate'), input('endDate'));
        }
        if (!empty($param['userId'])) {
            $where['userId'] = $param['userId'];
        }
        $pageSize = !empty($param['pageSize']) ? $param['pageSize'] : 10;
        $res = Db::name('users_entrance')->where($where)
            ->order('id')->paginate($pageSize)->toArray();
        return $res;

    }


    public function add()
    {

        $param = input('post.');
        if (empty($param['id'])) {
            unset($param['id']);
        }
        $param['staffId'] = session('sk_staff')['staffId'];
        $param['ip'] = get_real_ip();
        $param['createTime'] = date('Y-m-d H:i:s');
        if (empty($param)) {
            return SKReturn('新增失败', -1);
        }
        $res = Db::name('users_entrance')->insert($param);
        if ($res) {
            return SKReturn('新增成功', 1);
        } else {
            return SKReturn('新增失败', -1);
        }


    }


}