<?php
/**
 * app虚拟币提币
 * User: Administrator
 * Date: 2018/5/31 0031
 * Time: 上午 9:22
 */

namespace app\api\model;

use think\Db;

class Draw extends Base
{
    /**
     * 获取数字货币地址列表
     * @return array
     * @author tiger 2018/05/9
     * @param $isList (0:分页；1：列表)
     * @param $uid  用户ID
     * @param $coin 货币简称
     * @param $page 页数
     * @param $pageSize 页量
     */
    public function lists($isList = 0, $uid = "", $coin = "", $page = 1, $pageSize = 10)
    {
        $where = [];
        $where['dataFlag'] = 1;
        $where['userId'] = $uid;
        if ($coin != '') {
            $where['coin'] = $coin;
        }
        if ($isList == 1) {
            $res = Db::name('coin_drawaddr')->where($where)->order("id desc")->select();
            return $res;
        }
    }

    /**
     * 虚拟币提现
     * @author tiger 2018/05/09
     * @param $coin 币种
     * @param $num 数量
     * @param $addr 提现地址
     * @param $payPwd 交易密码
     * @param $userId 用户Id
     */
    public function draw($userId,$param)
    {
        $must = ["coin","num","addr","zcFee"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }

        $num = $param['num'];
        $addr = $param['addr'];
        $coin = $param['coin'];
        $zcFee = $param['zcFee'];

        if(SysConfig('minDraw') > $num){
            return SKReturn("最小提现量:".SysConfig('minDraw'));
        }
        if(SysConfig('maxDraw') < $num){
            return SKReturn("最大提现量:".SysConfig('maxDraw'));
        }

        $in_coin = Db::name('coin')->field('zcFee')->where(['coin' => $coin])->find();
        if (moneyFormat($in_coin['zcFee']) !== moneyFormat($zcFee)) {
            return SKReturn("手续费有误！");
        }
        //操作前查询一次用户钱包对应的币种资产
        $users_coina = Db::name('users_coin')->where(['userId' => $userId, 'coin' => $coin])->find();
        if (empty($users_coina)) {
            return SKReturn("请选择正确的操作币种");
        }
        if ($num > $users_coina['black']) {
            return SKReturn("提现的币种数量不足");
        }
        $orderNO = SKOrderSn('b');
        $coin_draw = [
            "userId" => $userId,
            "coin" => $coin,
            "orderNo" => $orderNO,
            "txHash"=>"",
            "coinAddr" => $addr,
            "num" => bcsub($num, $in_coin['zcFee'], 4),
            "miner" => 0,
            "fee" => $zcFee,
            "radio" => 0,
            "status" => 0,
            "createTime" => date("Y-m-d H:i:s"),
            "createIp" => '',
            "dataFlag" => 1,
        ];
        $update['black'] = Db::raw('black-'.$num);
        $update['forzen'] = Db::raw('forzen+'.$num);

        Db::startTrans();
        try {
            //用户数字货币提现表
            Db::name('coin_draw')->insert($coin_draw);
            //用户在平台对应的冻结中的币种钱包资产增加，可用币增加减少
            Db::name('users_coin')->where(['userId' => $userId, 'coin' => $coin])->update($update);
            //生成一条最新的用户账户币种流水
            insertLog($coin,$userId,$orderNO,6,'-'.$num,0.0000,$num,'用户虚拟币提现申请');
            Db::commit();
            return SKReturn("提现申请成功",1);
        } catch (Exception $e) {
            Db::rollback();
            $msg = $e->getMessage();
            return SKReturn($msg);
        }
    }
}