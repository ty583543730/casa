<?php
/**
 * 分佣奖励.
 * User: wjj
 * Date: 2018/6/1
 * Time: 9:12
 */

namespace app\admin\model;

use Think\Db;

class Rewards extends Base
{
    /**
     * 分页
     */
    public function pageQuery()
    {
        $where = [];
        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['createTime'] = timeTerm(input('startDate'), input('endDate'));
        }
        $type = input('get.type', '');
        if ($type !== '') {
            $where['type'] = $type;
        }

        $data = $this
            ->field('id,orderNo,type,total,num,createTime')
            ->where($where)
            ->order('id desc')
            ->paginate(input('limit'))
            ->toArray();
        return $data;
    }

    /*奖励详情*/
    public function getInfo($id)
    {
        $info = Db::name('rewards')
            ->field('orderNo,total,type,num,score,binding,locker,createTime')
            ->where(['id' => $id])
            ->find();
        return $info;
    }
}