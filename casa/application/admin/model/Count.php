<?php
/**
 * 平台数据统计.
 * User: wjj
 * Date: 2018/6/6
 * Time: 9:35
 */

namespace app\admin\model;

use Think\Db;

class Count extends Base
{
    /*用户统计分页*/
    public function userPageQuery()
    {
        $where = [];
        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['date'] = timeTerm(input('startDate'), input('endDate'), 1);
        }
        $date = Db::name('date_users')
            ->field('id', true)
            ->where($where)
            ->order('id', 'desc')
            ->paginate(input('limit/d'))
            ->toArray();
        return $date;
    }

    /*订单统计分页*/
    public function transPageQuery()
    {
        $where = [];
        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['date'] = timeTerm(input('startDate'), input('endDate'), 1);
        }
        $date = Db::name('date_transfers')
            ->field('id,date,transferNumLock,transferCoinLock,transferNumTotalLock,transferCoinTotalLock')
            ->where($where)
            ->order('id', 'desc')
            ->paginate(input('limit/d'))
            ->toArray();
        return $date;
    }

    /*订单统计详情*/
    public function transInfo()
    {
        $id = input('id');
        $data = Db::name('date_transfers')->where('id', $id)->field('id', true)->find();
        return $data;
    }

    /*币种统计分页*/
    public function coinPageQuery()
    {
        $where = [];
        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['date'] = timeTerm(input('startDate'), input('endDate'), 1);
        }
        if (input('coin')) {
            $where['coin'] = input('coin');
        }
        $date = Db::name('date_coins')
            ->field('id', true)
            ->where($where)
            ->order('id', 'desc')
            ->paginate(input('limit/d'))
            ->toArray();
        return $date;
    }

    /*系统对账分页*/
    public function checkPageQuery()
    {
        $where = [];
        if (input('coin')) {
            $where['coin'] = input('coin');
        }
        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['date'] = timeTerm(input('startDate'), input('endDate'), 1);
        }
        $date = Db::name('check_result')
            ->field('*')
            ->where($where)
            ->order('id', 'desc')
            ->paginate(input('limit/d'))
            ->toArray();
        return $date;
    }

    /*对账详情*/
    public function getInfo()
    {
        $id = input('id');
        $data = Db::name('check_result')->where('id', $id)->field('id', true)->find();
        return $data;
    }

    /*系统对账分页*/
    public function detailPageQuery()
    {
        $where = [];
        if (input('coin')) {
            $where['coin'] = input('coin');
        }
        $userPhone = trim(input('get.userPhone'));
        if ($userPhone != '') {
            $where['userPhone'] = $userPhone;
        }
        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['date'] = timeTerm(input('startDate'), input('endDate'), 1);
        }
        $date = Db::name('check_detail')->alias('d')
            ->join('users s', 's.userId=d.userId', 'left')
            ->field('id,userPhone,coin,date,block,lock,forzen,blockDiff,lockDiff,forzenDiff,blockChange,lockChange,forzenChange')
            ->where($where)
            ->order('id', 'desc')
            ->paginate(input('limit/d'))
            ->toArray();
        return $date;
    }

    /*每日用户对账详情*/
    public function getDetail()
    {
        $id = input('id');
        $data = Db::name('check_detail')->where('id', $id)->field('id', true)->find();
        return $data;
    }

    /*获取首页统计数据*/
    public function getCount()
    {
        //待办实名审核
        $waitRealName = Db::name('users_realname')->where(['checkStatus' => 0])->count();
        //待提现审核
        $waitDraw = Db::name('coin_draw')->where(['status' => 0])->count();
        //今日注册人数
        $registerToday = Db::name('users')->where(['createTime' => ['between', getDateTime('today')]])->count();
        //今日区长增量
        $wardenToday = Db::name('users_upgrade')->where(['afterRole' => 2, 'createTime' => ['between', getDateTime('today')]])->count();
        //今日管理员增量
        $adminToday = Db::name('users_upgrade')->where(['afterRole' => 3, 'createTime' => ['between', getDateTime('today')]])->count();
        //获取当月数据
        $month = date('Ym');
        $mouthData = Db::name('date_users')
            ->where(['month' => $month])
            ->field('sum(userNum) as registerMonth,sum(xitongNum) as adminMonth')
            ->find();
        //获取总数据
        $countData = Db::name('date_users')
            ->where(['date' => date('Ymd', strtotime("-1 day"))])
            ->field('userNumTotal,xitongTotal')
            ->find();
        $data = array(
            'waitRealName' => $waitRealName,
            'waitDraw' => $waitDraw,
            'registerToday' => $registerToday,
            'wardenToday' => $wardenToday,
            'adminToday' => $adminToday,
            'registerMonth' => $mouthData['registerMonth'] + $registerToday,
            'adminMonth' => $mouthData['adminMonth'] + $adminToday,
            'userNumTotal' => $countData['userNumTotal'] + $registerToday,
            'xitongTotal' => $countData['xitongTotal'] + $adminToday
        );
        return $data;
    }

    public function registerCount()
    {
        $monCount = array();
        $tDate = date('Y-m-d 00:00:00');
        //获取当月注册量
        $ThisMonData = Db::name('users')->where(['createTime' => ['>', date('Y-m-00 00:00:00')]])->count();
        for ($i = 6; $i > 0; $i--){
            $monData[date('m月',strtotime("-$i months",strtotime($tDate)))] = Db::name('date_users')->where(['month'=>date('Ym',strtotime("-$i months",strtotime($tDate)))])->sum('userNum');
        }
        $monData[date('m月')] = $ThisMonData;
        foreach ($monData as $k => $v){
            $monCount['date'][] = $k;
            $monCount['monCount'][] = $v;
        }

        $dayCount = array();
        //获取当日注册量
        $thisDayData = Db::name('users')->where(['createTime'=>['>',$tDate]])->count();
        //历史每日统计
        $dateUser = Db::name('date_users')->field('date,userNum')->limit(6)->order('id asc')->select();
        $response = array();
        //如果历史数据小于6 则补余
        $count = count($dateUser);
        if($count < 6){
            //获取离今天最远一天的日期
            $sDay = empty($dateUser) ? date('Y-m-d') : $dateUser[0]['date'];
            for($i=(6-$count);$i>=1;$i--){
                array_push($response,['date'=>date('Y-m-d',strtotime("-$i day",strtotime($sDay))),'userNum'=>0]);
            }
            if($count>0){
                foreach ($dateUser as $value){
                    array_push($response,$value);
                }
            }
        }else{
            $fullUser = Db::name('date_users')->field('date,userNum')->limit(7)->order('id desc')->select();
            $response = array_reverse($fullUser,true);
        }
        foreach ($response as $k => $v) {
            $dayCount['date'][] = date('d日', strtotime($v['date']));
            $dayCount['userNum'][] = $v['userNum'];
        }
        array_push($dayCount['date'],date('d日'));
        array_push($dayCount['userNum'],$thisDayData);
       $count = array('dayCount'=>$dayCount,'monCount'=>$monCount);
        return $count;

    }

    /*用户统计数据导出*/
    public function exportUser()
    {
        $where = [];
        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['date'] = timeTerm(input('startDate'), input('endDate'), 1);
        }
        $res = Db::name('date_users')->field('id', true)->where($where)->select();
        $dataExcel = array();
        foreach ($res as $k => $v) {
            $rowData = array($v['date'], $v['userNum'], $v['userNumTotal'], $v['xitongNum'], $v['xitongTotal']);
            array_push($dataExcel, $rowData);
        }
        return $dataExcel;
    }

    /*订单统计数据导出*/
    public function exportTrans()
    {
        $where = [];
        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['date'] = timeTerm(input('startDate'), input('endDate'), 1);
        }
        $res = Db::name('date_transfers')->field('id', true)->where($where)->select();
        $dataExcel = array();
        foreach ($res as $k => $v) {
            $rowData = array($v['date'], $v['transferNum'], $v['transferCoin'], $v['transferNumTotal'], $v['transferCoinTotal'], $v['paymentNum'], $v['paymentCoin'], $v['paymentNumTotal'], $v['paymentCoinTotal'], $v['transferNumLock'], $v['transferCoinLock'], $v['transferNumTotalLock'], $v['transferCoinTotalLock'], $v['paymentNumLock'], $v['paymentCoinLock'], $v['paymentNumTotalLock'], $v['paymentCoinTotalLock']);
            array_push($dataExcel, $rowData);
        }
        return $dataExcel;
    }

    /*资产统计数据导出*/
    public function exportCoin()
    {
        $where = array();

        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['date'] = timeTerm(input('startDate'), input('endDate'), 1);
        }
        if (input('coin')) {
            $where['coin'] = input('coin');
        }
        $res = Db::name('date_coins')->field('id', true)->where($where)->select();
        $dataExcel = array();
        foreach ($res as $k => $v) {
            $rowData = array($v['date'], $v['coin'], $v['recharge'], $v['draw'], $v['rechargeTotal'], $v['drawTotal']);
            array_push($dataExcel, $rowData);
        }
        return $dataExcel;
    }

    /*平台对账差异导出*/
    public function exportCheck()
    {
        $where = array();
        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['date'] = timeTerm(input('startDate'), input('endDate'), 1);
        }
        if (input('coin')) {
            $where['coin'] = input('coin');
        }
        $res = Db::name('check_result')
            ->field('date,coin,block,lock,forzen,blockDiff,lockDiff,forzenDiff,blockChange,lockChange,forzenChange')
            ->where($where)
            ->select();
        $dataExcel = array();
        foreach ($res as $k => $v) {
            $rowData = array($v['date'], $v['coin'], $v['block'], $v['lock'], $v['forzen'], $v['blockDiff'], $v['lockDiff'], $v['forzenDiff'], $v['blockChange'], $v['lockChange'], $v['forzenChange']);
            array_push($dataExcel, $rowData);
        }
        return $dataExcel;
    }

    /*用户日对账差异导出*/
    public function exportDetail()
    {
        $where = array();
        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['date'] = timeTerm(input('startDate'), input('endDate'), 1);
        }
        if (input('coin')) {
            $where['coin'] = input('coin');
        }
        $res = Db::name('check_detail')->alias('d')
            ->join('users u', 'u.userId=d.userId', 'left')
            ->field('userName,date,coin,block,lock,forzen,blockDiff,lockDiff,forzenDiff,blockChange,lockChange,forzenChange')
            ->where($where)
            ->order('id desc')
            ->select();
        $dataExcel = array();
        foreach ($res as $k => $v) {
            $rowData = array($v['userName'], $v['date'], $v['coin'], $v['block'], $v['lock'], $v['forzen'], $v['blockDiff'], $v['lockDiff'], $v['forzenDiff'], $v['blockChange'], $v['lockChange'], $v['forzenChange']);
            array_push($dataExcel, $rowData);
        }
        return $dataExcel;
    }
}