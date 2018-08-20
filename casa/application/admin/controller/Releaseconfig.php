<?php
/**
 * 锁仓币释放
 * User: tuyi
 * Date: 2018/8/2
 */

namespace app\admin\controller;

use app\admin\model\ReleaseConfig As R;

class Releaseconfig extends Base
{
    /**
     * 锁仓币释放配置列表
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

    public function add()
    {
        $m = new R();
        if (request()->isAjax()) {
            return $m->add();
        } else {
            $releaseInfo = $m->pageQuery();
            $this->assign('data', $releaseInfo['data'][0]);
            return $this->fetch('releaseconfig/add');
        }

    }

    public function del()
    {
        $m = new R();
        if (request()->isAjax()) {
            return $m->del();
        }
    }


}