<?php
/**
 * 储蓄
 * User: tuyi
 * Date: 2018/8/2
 */

namespace app\admin\controller;

use app\admin\model\Savings As S;

class Savings extends Base
{
    public function index()
    {
        if (request()->isAjax()) {
            $m = new S();
            $rs = $m->pageQuery();
            return PQReturn($rs);
        } else {
            return $this->fetch('index');
        }
    }


}