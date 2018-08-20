<?php
/**
 *平台数据统计.
 * User: wjj
 * Date: 2018/6/6
 * Time: 9:33
 */

namespace app\admin\controller;

use app\admin\model\Count as C;

class Count extends Base
{
    /*用户统计*/
    public function userCount()
    {
        if (request()->isAjax()) {
            $c = new C();
            $rs = $c->userPageQuery();
            return PQReturn($rs);
        } else {
            return $this->fetch('userCount');
        }
    }

    /*订单统计*/
    public function transCount()
    {
        if (request()->isAjax()) {
            $c = new C();
            $rs = $c->transPageQuery();
            return PQReturn($rs);
        } else {
            return $this->fetch('transCount');
        }
    }

    /*订单统计详情*/
    public function transInfo()
    {
        $c = new C();
        $data = $c->transInfo();
        $this->assign('data', $data);
        return $this->fetch('transInfo');
    }

    /*币种统计*/
    public function coinCount()
    {
        if (request()->isAjax()) {
            $c = new C();
            $rs = $c->coinPageQuery();
            return PQReturn($rs);
        } else {
            $this->assign('coinList', SKBtcCoin());
            return $this->fetch('coinCount');
        }
    }

    /*系统对账结果*/
    public function checkResult()
    {
        if (request()->isAjax()) {
            $c = new C();
            $rs = $c->checkPageQuery();
            return PQReturn($rs);
        } else {
            $this->assign('coinList', SKBtcCoin());
            return $this->fetch('checkResult');
        }
    }

    /*对账详情*/
    public function checkInfo()
    {
        $c = new C();
        $data = $c->getInfo();
        $this->assign('data', $data);
        return $this->fetch('checkInfo');
    }

    /*每日用户对账结果*/
    public function checkDetail()
    {
        if (request()->isAjax()) {
            $c = new C();
            $rs = $c->detailPageQuery();
            return PQReturn($rs);
        } else {
            $this->assign('coinList', SKBtcCoin());
            return $this->fetch('detailResult');
        }
    }

    /*每日用户对账详情*/
    public function detailInfo()
    {
        $c = new C();
        $data = $c->getDetail();
        $this->assign('data', $data);
        return $this->fetch('detailInfo');
    }

    //用户统计数据导出
    public function exportUser()
    {
        $c = new C();
        $dataExcel = $c->exportUser();
        $title = array("日期", "新注册用户", "总注册用户", "新增区长数", "总区长数", "新增管理员数", "总管理员数");
        exportExcel($dataExcel, $title, '用户统计表');
    }

    //订单统计数据导出
    public function exportTrans()
    {
        $c = new C();
        $dataExcel = $c->exportTrans();
        $title = array("日期", "转账订单数量", "转账数字币数量", "转账总订单数量", "转账总数字币数量", "收款订单数量", "收款数字币数量", "收款总订单数量", "收款总数字币数量", "可用币转账订单数量", "可用币转账数字币数量", "可用币转账总订单数量", "可用币转账总数字币数量", "锁仓币收款订单数量", "锁仓币收款数字币数量", "锁仓币收款总订单数量", "锁仓币收款总数字币数量");
        exportExcel($dataExcel, $title, '订单统计表');
    }

    //资产统计数据导出
    public function exportCoin()
    {
        $c = new C();
        $dataExcel = $c->exportCoin();
        $title = array("日期", "币种", "当日充值数量", "当日提现数量", "总充值数量", "总提现数量");
        exportExcel($dataExcel, $title, '资产统计表');
    }

    //平台对账差异导出
    public function exportCheck()
    {
        $c = new C();
        $dataExcel = $c->exportCheck();
        $title = array("日期", "币种", "可用币数量", "锁定币数量", "冻结币数量", "可用对账差异", "锁仓对账差异", "冻结对账差异", "可用变化数量", "锁仓变化数量", "冻结变化数量");
        exportExcel($dataExcel, $title, '对账结果表');
    }

    //平台用户日对账差异导出
    public function exportDetail()
    {
        $c = new C();
        $dataExcel = $c->exportDetail();
        $title = array("用户名", "日期", "币种", "可用币数量", "锁定币数量", "冻结币数量", "可用对账差异", "锁仓对账差异", "冻结对账差异", "可用变化数量", "锁仓变化数量", "冻结变化数量");
        exportExcel($dataExcel, $title, '用户日对账结果表');
    }
}