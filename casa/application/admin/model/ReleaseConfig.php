<?php
/**
 * 锁仓币释放参数配置
 * User: tuyi
 * Date: 2018/8/2
 */

namespace app\admin\model;

use think\Db;

class ReleaseConfig extends Base
{
    /**
     * 锁仓币释放列表分页
     * @return array
     * @author tuyi
     * Date: 2018/8/2
     */
    public function pageQuery()
    {
        $param = input('get.');
        $pageSize = isset($param['limit']) ? $param['limit'] : 10;
        $page = Db::name('release_config')
            ->field('id,min,max,baseRatio,addRatio,addTop,createTime')
            ->order('id asc')
            ->paginate($pageSize)
            ->toArray();
        return $page;
    }

    /**
     * 新增、编辑锁仓币释放信息
     * User: zhouying
     * Date: 2018/5/17
     */
    public function add()
    {
        $param = input("post.");
        if (!empty($param['id'])) {
            $where['id'] = $param['id'];
            $res = Db::name('release_config')->where($where)->update($param);
            if ($res == 1) {
                return SKReturn('更新成功', 1);
            } else {
                return SKReturn('数据未改变，更新失败', -1);
            }
        } else {
            $res = Db::name('release_config')->insert($param);
            if ($res) {
                return SKReturn('新增成功', 1);
            } else {
                return SKReturn('新增失败', -1);
            }
        }

    }

    public function del()
    {
        $param = input("post.");
        if (!empty($param['id'])) {
            $where['id'] = $param['id'];
            $is_set = Db::name('release_config')->where($where)->find();
            if ($is_set) {
                $res = Db::name('release_config')->where($where)->delete();
                if ($res) {
                    return SKReturn('删除成功', 1);
                }
            } else {
                return SKReturn('数据不存在', -8);
            }
        }
    }


}