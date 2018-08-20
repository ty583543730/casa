<?php
/**
 * 新增业绩奖励
 * User: tiger
 * Date: 2018/8/10
 * Time: 9:31
 */

namespace app\common\command;


use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\Log;
use think\Exception;

class Entrance extends Command
{

    protected function configure()
    {
        $this->setName('Entrance')->setDescription('Entrance');
    }

    protected function execute(Input $input, Output $output)
    {
        $savingsReturnRatio = SysConfig('addRewardsRatio'); //新增业绩奖励比例

        //获取昨天所有的入场数据
        $betweenTime = getDateTime('yesterday');
        $users_entrance = Db::name('users_entrance')->where(['createTime'=>['between time',$betweenTime]])->select();

        $data = [];
        //先对所有下级进行归类分组，父级相同为一组
        foreach ($users_entrance as $v) {
            $parentId = Db::name('users')->where(['userId' => $v['userId']])->value('parentId');
            $data[$parentId][] = bcmul($v['num'], $v['marketValue'], 4);
        }

        if (!empty($data)) {
            //对每一个父级进行奖励
            foreach ($data as $key => $val) {
                //倒序排序
                rsort($val, 1);

                if (!empty($val[1])) {
                    $total = $val[1];
                } else {
                    $total = $val[0];
                }

                $num = bcmul($total, $savingsReturnRatio, 4);

                if ($num > 0) {
                    $rewards[] = [
                        'type' => 3,
                        'orderNo' => SKOrderSn('x'),
                        'userId' => $key,
                        'total' => $total,
                        'ratio' => $savingsReturnRatio,
                        'num' => $num,
                    ];
                }
            }
        }
        if (!empty($rewards)) {
            Db::startTrans();
            try {
                $res = BatchRewards($rewards);
                if ($res) {
                    $output->writeln('Entrance Exe success');
                }
                Db::commit();
                $output->writeln('Entrance Exe fail');
            }catch (Exception $e){
                Db::rollback();
                Log::logger('error','Entrance Exe error:'.$e->getMessage(),'shell');
                $output->writeln('Entrance Exe error:'.$e->getMessage());
            }
        }else{
            $output->writeln('Entrance rewards is empty');
        }
    }
}