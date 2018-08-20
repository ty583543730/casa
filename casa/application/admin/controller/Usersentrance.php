<?php
/**
 * 入场数据
 * User: tuyi
 * Date: 2018/8/10
 */

namespace app\admin\controller;

use app\admin\model\UsersEntrance As U;

class Usersentrance extends Base
{
    /**
     * 用户类型列表
     * @return mixed|string
     * @author tuyi
     */
    public function index()
    {
        if (request()->isAjax()) {
            $u = new U();
            $data = $u->getDatas();
            return PQReturn($data);
        }
        return $this->fetch("index");
    }

    /**
     * 新增页面
     */
    public function add()
    {
        $param = input('get.');
        if (isset($param['id']) && $param['id'] == 0) {
            return $this->fetch('usersentrance/add');
        } else {
            $u = new U();
            return $u->add();
        }

    }


}