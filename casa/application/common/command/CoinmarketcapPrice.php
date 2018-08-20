<?php
/**
 * 采集程序 币种的价格
 * User: yt
 * Date: 2018/5/29 0029
 * Time: 下午 6:11
 */

namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class CoinmarketcapPrice extends Command
{
    protected function configure()
    {
        $this->setName('CoinmarketcapPrice')->setDescription('get coinmarketcap website coin price');
    }

    protected function execute(Input $input, Output $output)
    {
        Db::execute('truncate sk_coinmarketcap_price');
        /*基础数据，usd，cny数据*/
        $url = 'https://api.coinmarketcap.com/v1/ticker/?convert=cny';
        $data = json_decode(file_get_contents($url), true);
        $data =array_slice($data,0,50);
        /*以太坊价格数据*/
        $url = 'https://api.coinmarketcap.com/v1/ticker/?convert=eth';
        $tmp = json_decode(file_get_contents($url), true);

        foreach ($data as $k=>$v){
            $data[$k]['price_eth']=$tmp[$k]['price_eth'];
            $data[$k]['24h_volume_eth']=$tmp[$k]['24h_volume_eth'];
            $data[$k]['market_cap_eth']=$tmp[$k]['market_cap_eth'];
            $data[$k]['last_updated'] =date('Y-m-d H:i:s',$v['last_updated']);
            unset($data[$k]['id']);
        }
        $res = Db::name('coinmarketcap_price')->insertAll($data);
        if($res == false){
            $output->writeln(date("m-d H:i:s") . ",CoinmarketcapPrice error!");
        }else{
            $output->writeln(date("m-d H:i:s") . ",CoinmarketcapPrice success!");
        }
    }
}