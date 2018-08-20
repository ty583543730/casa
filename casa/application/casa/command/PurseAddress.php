<?php
/**
 * 数字货币钱包地址定时业务处理
 * Created by PhpStorm.
 * User: tiger
 * Date: 2018/3/29
 * Time: 14:22
 */

namespace app\casa\command;

use app\casa\model\BlockChain as C;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Option;

//新增钱包地址
class PurseAddress extends Command
{
    protected function configure()
    {
        $this->setName('PurseAddress')->setDescription('新增钱包地址');
//        $this->addOption('coin', 'c', Option::VALUE_OPTIONAL, ''); //选项值选填
    }

    protected function execute(Input $input, Output $output)
    {
        //获取选项值
//        $options = $input->getOptions();
        $time = date('Y-m-d H:i:s');
        $m = new C();
        $res = $m->newAccount();
        dump($res);
        $output->writeln($time . "执行完毕");
    }
}