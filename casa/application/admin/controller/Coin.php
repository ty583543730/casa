<?php
/**
 * 币种管理
 * User: Administrator
 * Date: 2018/5/17 0023
 * Time: 下午 2:08
 */

namespace app\admin\controller;

use app\admin\model\Coin As C;

class Coin extends Base
{
    /**
     * 币种列表
     * @return mixed|string
     * @author zhouying
     */
    public function index()
    {
        if (request()->isAjax()) {
            $param = array_filter(trimArray(input('get.')));
            $m = new C();
            $rs = $m->pageQuery($param);
            return PQReturn($rs);
        } else {

            return $this->fetch('index');
        }
    }

    /**
     * 新增、编辑币种
     * User: zhouying
     * Date: 2018/5/17
     */
    public function addCoin()
    {
        if (request()->isAjax()) {
            $m = new C();
            return $m->addCoin();
        } else {
            $id = input("get.id", "0");
            $m = new C();
            $coinInfo = $m->getCoin($id);
            $this->assign('coin', $coinInfo);
            return $this->fetch('coin/addCoin');
        }
    }

    /**
     * 修改币种状态
     * User: zhouying
     * Date: 2018/5/17
     */
    public function changeStatus()
    {
        $m = new C();
        return $m->changeStatus();
    }

    /**
     * 系统钱包列表
     * @return mixed|string
     * @author tiger
     */
    public function indexSys()
    {
        if (request()->isAjax()) {
            $param = array_filter(trimArray(input('get.')));
            $m = new C();
            $rs = $m->syspageQuery($param);
            return PQReturn($rs);
        } else {
            return $this->fetch('coin/indexsys');
        }
    }

    /**
     * 钱包余额转入转出页面
     * User: tiger
     */
    public function change()
    {
        $id = input("get.id", "0");
        $m = new C();
        $sysBank = $m->sysBank($id);
        $this->assign('status', input("get.status"));
        $this->assign('sysBank', $sysBank);
        return $this->fetch('coin/change');

    }

    /**
     * 钱包余额转入转出
     * User: tiger
     */
    public function doChange()
    {
        $m = new C();
        return $m->doChange();
    }

    /**
     * 兑换管理
     * @return mixed|string
     * @author tiger
     * 2018-6-21
     */
    public function exchange()
    {
        if (request()->isAjax()) {
            $param = array_filter(trimArray(input('get.')));
            $m = new C();
            $rs = $m->exchangePageQuery($param);
            return PQReturn($rs);
        } else {
            return $this->fetch('exchange');
        }
    }

    /**
     * 可划扣列表
     * @return mixed|string
     * @author tiger
     * 2018-6-21
     */
    public function delimit()
    {
        if (request()->isAjax()) {
            $param = array_filter(trimArray(input('get.')));
            $m = new C();
            $rs = $m->delimitPageQuery($param);
            return PQReturn($rs);
        } else {
            return $this->fetch('delimit');
        }
    }

    /**
     * 不可划扣列表
     * @return mixed|string
     * @author tiger
     * 2018-6-21
     */
    public function notDelimit()
    {
        if (request()->isAjax()) {
            $param = array_filter(trimArray(input('get.')));
            $m = new C();
            $rs = $m->notDelimitPageQuery($param);
            return PQReturn($rs);
        } else {
            return $this->fetch('notdelimit');
        }
    }

    /**
     * 设置已转手续费字段状态
     * @return mixed|string
     * @author tiger
     * 2018-6-21
     */
    public function setStatus()
    {
        $m = new C();
        $rs = $m->setStatus();
        return $rs;
    }

}