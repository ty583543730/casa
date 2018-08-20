<?php
/**
 * 算力的日志
 * User: tuyi
 * Date: 2018/8/10
 */

namespace app\admin\controller;

use app\admin\model\LogCp As L;

class Logcp extends Base
{
    /**
     * 用户类型列表
     * @return mixed|string
     * @author tuyi
     */
    public function index()
    {
        if (request()->isAjax()) {
            $u = new L();
            $data = $u->getDatas();
            return PQReturn($data);
        }
        return $this->fetch("index");
    }


}