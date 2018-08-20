<?php
/**
 * 锁仓币释放
 * User: tuyi
 * Date: 2018/8/2
 */

namespace app\admin\controller;

use app\admin\model\Release As R;

class Release extends Base
{
    /**
     * 锁仓币释放列表
     * @return mixed|string
     * @author zhouying
     */
    public function index()
    {
        if (request()->isAjax()) {
            $m = new R();
            $rs = $m->pageQuery();
              return PQReturn($rs);
        } else {
            return $this->fetch('index');
        }


    }


}
