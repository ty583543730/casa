<?php
/**
 * 锁仓币释放参数配置
 * User: tuyi
 * Date: 2018/8/2
 */

namespace app\admin\model;

use think\Db;

class Release extends Base
{
    /**
     * 锁仓币释放列表分页
     * @return array
     * @author zhouying
     * Date: 2018/5/17
     */
    public function pageQuery()
    {
        $param = array_filter(trimArray(input('get.')));
        $where = [];
        $pageSize = isset($param['limit']) ? $param['limit'] : 10;
        if (!empty($param['userId'])) {
            $where['userId'] = $param['userId'];
        }
        $page = Db::name('release')
            ->field('id,userId,ownNum,baseRatio,turnover,addRatio,num,createTime')
            ->where($where)
            ->order('id desc')
            ->paginate($pageSize)
            ->toArray();
        return $page;
    }

}