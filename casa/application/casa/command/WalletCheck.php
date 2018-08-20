<?php
/**
 * Created by PhpStorm.
 * User: tiger
 * Date: 2018-4-10 14:54
 */

namespace app\casa\command;

use app\casa\model\CurrencyScript as S;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class WalletCheck extends Command
{
    protected function configure()
    {
        //一分钟执行一次
        $this->setName('WalletCheck')->setDescription('钱包余额检查、转账申请、交易状态查询');
    }

    protected function execute(Input $input, Output $output)
    {
        $s = new S();
        //充值审核
        $this->autoRecharge();

        //自动转出申请
        if (date("H:i") == "01:05") {
        $s->turnOut();
        }

        //上一步结果查询
        if (date("H:i") == "01:05") {
        $s->turnResult();
        }
        //发起提现交易申请
        if (fmod(date("i"), 50) == 0) {
            $s->drawReplay();
        }

        //确认提现申请操作结果
        if (fmod(date("i"), 50) == 0) {
            $s->drawResult();
        }
    }

    public function autoRecharge(){
        $s = new S();
        if (cache("WalletCheck") == 1) {
            return "";
        }
        cache("WalletCheck", 1, 600);
        $s->autoRecharge();
        cache("WalletCheck", null);
    }
}