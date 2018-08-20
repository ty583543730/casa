<?php

namespace app\admin\model;

use think\Db;

/**
 * 登录日志业务处理
 */
class LogStaffLogins extends Base
{
    /**
     * 分页
     */
    public function pageQuery()
    {
        $where = [];

        $staffName = trim(input('get.staffName'));
        if ($staffName != '') $where['s.staffName'] = ['like', '%' . $staffName . '%'];

        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['l.loginTime'] = timeTerm(input('startDate'), input('endDate'));
        }
        return Db::name('log_staff_logins')->alias('l')
            ->join('staffs s', ' l.staffId=s.staffId', 'left')
            ->where($where)
            ->field('l.*,s.staffName')
            ->order('l.loginId', 'desc')
            ->paginate(input('limit/d'))
            ->toArray();

    }
}
