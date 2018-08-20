<?php
/**
 * 交易所数字币划拨
 * User: tuyi
 * Date: 2018/8/2
 */

namespace app\admin\controller;

use app\admin\model\TransferExchange As T;

class Transferexchange extends Base
{
    public function index()
    {
        if (request()->isAjax()) {
            $m = new T();
            $rs = $m->pageQuery();
            if (empty($rs['data'])) {
                return SKReturn('未查询到相关数据');
            }
            return PQReturn($rs);
        } else {

            return $this->fetch('index');
        }
    }


}