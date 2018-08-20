<?php
/**
 * 用户升级模型.
 */

namespace app\admin\model;

class UsersUpgrade extends Base
{
    private $userType = [1 => '普通', 2 => '区长', 3 => '系统管理员'];

    /* 分页*/
    public function pageQuery()
    {
        $where = [];
        $userId = input('get.userId/d', 0);
        if ($userId !== 0) {
            $where['a.userId'] = $userId;
        }
        $userPhone = input('get.userPhone', '');
        if ($userPhone !== '') {
            $where['u.userPhone'] = $userPhone;
        }
        $pageSize = input('limit', 20);

        $type = input('get.type', '');
        if ($type != '') {
            $where['a.type'] = $type;
        }
        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['a.createTime'] = timeTerm(input('startDate'), input('endDate'));
        }

        $data = $this->alias('a')
            ->join('users u', 'u.userId=a.userId')
            ->field('a.*,u.userPhone')
            ->where($where)
            ->order('a.id', 'asc')
            ->paginate($pageSize)
            ->toArray();

        foreach ($data['data'] as $k => $v) {
            $data['data'][$k]['preRole'] = $this->userType[$v['preRole']];
            $data['data'][$k]['afterRole'] = $this->userType[$v['afterRole']];
        }
        return $data;

    }

}