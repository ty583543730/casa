<?php

/**
 * 用户类型
 * User: tuyi
 * Date: 2018/8/9
 */

namespace app\admin\model;

use think\Db;
use think\Loader;

/**
 * 用户类型业务处理
 */
class UsersType extends Base
{
    public function getAllTypes()
    {
        $param = input('post.');
        $pageSize = isset($param['pageSize']) ? $param['pageSize'] : 10;
        $res = Db::name('users_type')
            ->order('id')->paginate($pageSize)->toArray();
        return $res;

    }


    /**
     * 查找单条记录
     */
    public function getOne()
    {
        $id = input('get.')['id'];
        if (is_null($id)) {
            return;
        }
        $res = Db::name('users_type')->where(array('id' => $id))->find();
        return $res;
    }

    public function edit()
    {
        $param = input('post.');
        if (empty($param['id'])) {
            $add_res = $this->add($param);
            return $add_res;
        } else {
            $where['id'] = $param['id'];
        }
        $res = Db::name('users_type')->where($where)->update($param);
        if ($res == 1) {
            return SKReturn('更新成功', 1);
        } else {
            return SKReturn('数据未改变，更新失败', -1);
        }

    }

    public function add($param)
    {
        if (empty($param)) {
            return SKReturn('新增失败', -1);
        }
        $res = Db::name('users_type')->insert($param);
        if ($res) {
            return SKReturn('新增成功', 1);
        } else {
            return SKReturn('新增失败', -1);
        }


    }

    public function del()
    {
        $param = input("post.");
        if (!empty($param['id'])) {
            $where['id'] = $param['id'];
            $is_set = Db::name('users_type')->where($where)->find();
            if ($is_set) {
                $res = Db::name('users_type')->where($where)->delete();
                if ($res) {
                    return SKReturn('删除成功', 1);
                }
            } else {
                return SKReturn('数据不存在', -8);
            }
        }
    }


}