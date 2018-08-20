<?php
/**
 * 交易所数字币划拨
 * User: tuyi
 * Date: 2018/8/2
 */

namespace app\admin\model;

use think\Db;

class TransferExchange extends Base
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
            $where['userId'] = array('=', $param['userId']);
        }
        if (!empty($param['orderNo'])) {
            $where['orderNo'] = array('like', $param['orderNo'] . '%');
        }
        $page = Db::name('transfer_exchange')
            ->field('id,userId,type,coin,total,orderNo,fee,num,ip,remark,createTime')
            ->where($where)
            ->where(['dataFlag' => 1])
            ->order('id desc')
            ->paginate($pageSize)
            ->toArray();
        return $page;
    }

}