<?php
/**
 * 系统消息
 * User: Administrator
 * Date: 2018/5/29 0029
 * Time: 上午 10:08
 */

namespace app\admin\model;


class SysMsgs extends Base
{
    /**
     * 分页
     */
    public function pageQuery()
    {
        $where = [];
        $where['a.dataFlag'] = 1;

        $key = trim(input('get.key'));
        if ($key != '') $where['a.msgTitle'] = ['like', '%' . $key . '%'];

        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['a.createTime'] = timeTerm(input('startDate'), input('endDate'));
        }
        return $this->alias('a')
            ->join('users b', 'a.userId=b.userId', 'left')
            ->field('a.id,a.msgTitle,a.msgStatus,a.createTime,b.userName')
            ->where($where)
            ->order('a.id', 'desc')
            ->paginate(input('limit/d'))
            ->toArray();
    }

    /**
     * 详情
     */
    public function getInfo($id)
    {
        $where = [];
        $where['a.dataFlag'] = 1;
        $where['a.id'] = $id;
        return $this->alias('a')
            ->join('users b', 'a.userId=b.userId', 'left')
            ->field('a.*,b.userPhone')
            ->where($where)
            ->find();
    }

    /**
     * 删除
     */
    public function del()
    {
        $id = input('post.id');
        $result = $this->setField(['id' => $id, 'dataFlag' => -1]);
        if ($result !== false) {
            return SKReturn('删除成功', 1);
        } else {
            return SKReturn('操作失败');
        }
    }
}