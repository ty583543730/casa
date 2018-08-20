<?php

namespace app\admin\model;

use think\Db;

/**
 * 操作日志业务处理
 */
class LogOperates extends Base
{
    /**
     * 分页
     */
    public function pageQuery()
    {
        $where = [];

        $staffName = trim(input('get.staffName'));
        if ($staffName != '') $where['s.loginName'] = ['like', '%' . $staffName . '%'];

        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['l.operateTime'] = timeTerm(input('startDate'), input('endDate'));
        }
        return Db::name('log_operates')->alias('l')->join('staffs s', ' l.staffId=s.staffId', 'left')
            ->join('menus m', ' l.menuId=m.menuId', 'left')
            ->where($where)
            ->field('l.*,s.staffName,m.menuName')
            ->order('l.operateId', 'desc')
            ->paginate(input('limit/d'))
            ->toArray();

    }

    /**
     *  获取指定的操作记录
     */
    public function getById($id)
    {
        $rs = $this->get($id);
        if (!empty($rs)) {
            return SKReturn('', 1, $rs);
        }
        return SKReturn('对不起，没有找到该记录', -1);
    }

    /**
     * 新增操作权限
     */
    public function add($param)
    {
        $data = [];
        $data['staffId'] = (int)session('sk_staff.staffId');
        $data['operateTime'] = date('Y-m-d H:i:s');
        $data['menuId'] = $param['menuId'];
        $data['operateDesc'] = $param['operateDesc'];
        $data['content'] = $param['content'];
        $data['operateUrl'] = $param['operateUrl'];
        $data['operateIP'] = $param['operateIP'];
        $this->create($data);
    }
}
