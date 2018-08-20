<?php

/**
 * 算力的日志
 * User: tuyi
 * Date: 2018/8/9
 */

namespace app\admin\model;

use think\Db;
use think\Loader;

/**
 * 用户类型业务处理
 */
class LogCp extends Base
{
    public function getDatas()
    {
        $param = input('get.');
        $where = [];
        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['createTime'] = timeTerm(input('startDate'), input('endDate'));
        }
        if (!empty($param['userId'])) {
            $where['userId'] = $param['userId'];
        }
        $pageSize = !empty($param['pageSize']) ? $param['pageSize'] : 10;
        $res = Db::name('log_cp')->where($where)
            ->order('id')->paginate($pageSize)->toArray();
        return $res;

    }


}