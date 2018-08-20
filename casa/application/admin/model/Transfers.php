<?php
/**
 * 转账付款
 * User: yt
 * Date: 2018/5/30 0030
 * Time: 下午 5:55
 */

namespace app\admin\model;


use think\Db;

class Transfers extends Base
{
    public $transfersType =[1=>'转账',2=>'收款'];

    /**
     * 分页
     */
    public function pageQuery()
    {
        $where = [];
        //付款方
        $sendPhone = input('get.sendPhone/d', 0);
        if ($sendPhone != '') {
            $where['b.userPhone'] = $sendPhone;
        }
        //收款方
        $userPhone = input('get.userPhone', '');
        if ($userPhone != '') {
            $where['c.userPhone'] = $userPhone;
        }

        $orderNo = input('get.orderNo', 0);
        if ($orderNo != '') {
            $where['a.orderNo'] = $orderNo;
        }

        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['a.createTime'] = timeTerm(input('startDate'), input('endDate'));
        }
        $type = input('get.coinType/d', 0);
        if ($type !== 0) {
            $where['a.coinType'] = $type;
        }

        $type = input('get.type/d', 0);
        if ($type !== 0) {
            $where['a.type'] = $type;
        }
        $data = Db::name('transfer_user')->alias('a')
            ->join('users b', 'a.sendId=b.userId', 'left')
            ->join('users c', 'a.userId=c.userId', 'left')
            ->field('a.*,b.userPhone as sendPhone,c.userPhone')
            ->where($where)
            ->order('a.id', 'desc')
            ->paginate(input('limit/d'))
            ->toArray();

        foreach ($data['data'] as $k => $v) {
            $data['data'][$k]['type'] = $this->transfersType[$v['type']];
        }
        return $data;
    }

    /**
     * 详情
     */
    public function getInfo($id)
    {
        $where = [];
        $where['a.id'] = $id;
        $info =  Db::name('transfer_user')->alias('a')
            ->join('users b', 'a.userId=b.userId', 'left')
            ->join('users c', 'a.sendId=c.userId', 'left')
            ->field('a.*,b.userName,b.userPhone,c.userName as sendName,c.userPhone as sendPhone')
            ->where($where)
            ->find();
        $info['type'] = $this->transfersType[$info['type']];
        return $info;
    }
}