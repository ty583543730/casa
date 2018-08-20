<?php
/**
 * 币种相关类
 * User: zhouying
 * Date: 2018/5/17
 * Time: 11:37
 */

namespace app\admin\model;
use think\Db;

class Coin extends Base
{
    private $hasFee = ['0'=>"未转",'1'=>"已转"];
    /**
     * 币种列表分页
     * @return array
     * @author zhouying
     * Date: 2018/5/17
     */
    public function pageQuery($param)
    {
        $pageSize = isset($param['limit']) ? $param['limit'] : 10;
        $page = Db::name('coin')
            ->field('id,coin,title,img,status,zrZt,zcZt')
            ->order('id desc')
            ->paginate($pageSize)
            ->toArray();
        return $page;
    }

    /**
     * 获取币种信息
     * User: zhouying
     * Date: 2018/5/17
     */
    public function getCoin($id){
        $res = Db::name('coin')
            ->where(['id' => $id])
            ->field('id,coin,title,img,ownHost,zrZt,zcZt,zcFee,isCurrency,superiorId')
            ->find();
        $res['superiorCoin'] = '';
        //获取的当前币种时代币
        if($res['isCurrency'] == 2){
            $res['superiorCoin'] = Db::name('coin')->where(['id' => $res['superiorId']])->value('coin');
        }
        return $res;
    }

    /**
     * 新增、编辑币种信息
     * User: zhouying
     * Date: 2018/5/17
     */
    public function addCoin(){
        $param = input('post.');
        if(empty($param['zcFee']))$param['zcFee'] = 0;
        $data = [
            'coin' => strtoupper($param['coin']),
            'title' => $param['title'],
            'img' => $param['img'],
            'ownHost' => $param['ownHost'],
            'zrZt' => $param['zrZt'],
            'zcFee' => $param['zcFee'],
            'zcZt' => $param['zcZt']
        ];
        if($param['superiorCoin'] == "NULL"){
            $data['isCurrency'] = 1;
            $data['superiorId'] = 0;
        }else{
            $data['isCurrency'] = 2;
            $data['superiorId'] = Db::name("coin")->where(['coin'=>$param['superiorCoin']])->value('id');
            if(empty($data['superiorId']))return SKReturn('请先添加该代币的货币币种信息');
        }
        //判断币种名字是否存在
        if (!empty($param['id'])) {
            $where['id'] = ['neq' , $param['id']];
        }
        $where['coin'] = $data['coin'];
        $result = Db::name("coin")->where($where)->value('coin');
        if (!empty($result)) {
            return SKReturn('该币种已存在', -1);
        }
        Db::startTrans();
        try{
            if ($param['id'] > 0) {
                Db::name("coin")->where(['id' => $param['id']])->update($data);
            } else {
                $data['createTime'] = date('Y-m-d H:i:s');
                Db::name('coin')->insert($data);
                //给所有用户新增该币种账户
                $userIds = Db::name('users')->where(['userId' =>['gt', 10000]])->column('userId');
                $list = [];
                foreach ($userIds as $k => $v) {
                    $list[$k] = ['userId' => $v, 'coin' => $data['coin']];
                }
                Db::name('user_coin')->insertAll($list);
                //新增系统数字币账号 类型 1:用户充值转入 2:用户提现转出 3:费用收入(用户提现) 4:费用支出 10机器人夺宝支出 11机器人夺宝中奖
                $systemData = [
                    ['sid' => 1, 'coin' => $data['coin'], 'black' => 0, 'addr' => '', 'name' => '', 'passwd' => '', 'createTime' => date('Y-m-d H:i:s')],
                    ['sid' => 2, 'coin' => $data['coin'], 'black' => 0, 'addr' => '', 'name' => '', 'passwd' => '', 'createTime' => date('Y-m-d H:i:s')],
                    ['sid' => 3, 'coin' => $data['coin'], 'black' => 0, 'addr' => '', 'name' => '', 'passwd' => '', 'createTime' => date('Y-m-d H:i:s')],
                    ['sid' => 4, 'coin' => $data['coin'], 'black' => 0, 'addr' => '', 'name' => '', 'passwd' => '', 'createTime' => date('Y-m-d H:i:s')],
                    ['sid' => 10, 'coin' => $data['coin'], 'black' => 10000, 'addr' => '', 'name' => '', 'passwd' => '', 'createTime' => date('Y-m-d H:i:s')],
                    ['sid' => 11, 'coin' => $data['coin'], 'black' => 0, 'addr' => '', 'name' => '', 'passwd' => '', 'createTime' => date('Y-m-d H:i:s')],
                 ];
                Db::name('coin_system')->insertAll($systemData);
                $tableName = 'sk_log_' . strtolower($data['coin']);
                $comment = strtolower($data['coin']) . '流水表';
                Db::query("CREATE TABLE IF NOT EXISTS ". $tableName ." LIKE sk_log_eth;");
                Db::query("ALTER TABLE ". $tableName ." COMMENT '". $comment ."';");
            }
            Db::commit();
            return SKReturn("操作成功!" ,1);
        }catch (\Exception $e){
            Db::rollback();
            return SKReturn("操作失败!");
        }
    }

    /**
     * 修改币种状态
     * User: zhouying
     * Date: 2018/5/17
     */
    public function changeStatus(){
        $param = input('post.');
        if (!in_array($param['status'], [-1,1])){
            return SKReturn("非法请求!");
        }
        $coin = Db::name('coin')->where(['id' => $param['id']])->value('id');
        if (empty($coin)){
            return SKReturn("该币种不存在!");
        }
        try{
            Db::name('coin')->where(['id' => $param['id']])->update(['status' => $param['status']]);
        }catch (\Exception $e){
            return SKReturn("修改状态失败!");
        }
        return SKReturn("修改状态成功!" ,1);

    }

    /**
     * 系统列表
     * @return mixed|string
     * @author tiger
     */
    public function syspageQuery()
    {
        $page = Db::name('coin_system')
            ->field('id,sid,coin,addr,name,black,total,createTime')
            ->order('id desc')
            ->where(['dataFlag'=>1])
            ->paginate(input('limit/d'))
            ->toArray();
        if (count($page['data']) > 0) {
            foreach ($page['data'] as $k => $v) {
                $logCoin = getLogTradeList("coin_system", true);
                $page['data'][$k]['sid'] = $logCoin[$v['sid']];
            }
        }
        return $page;
    }

    /**
     * 钱包余额转入转出页面
     * User: tiger
     */
    public function sysBank($id){
        $res = Db::name('coin_system')
            ->where(['id' => $id])
            ->field('id,coin,addr,name,black,total,createTime')
            ->find();
        return $res;
    }

    /**
     * 修改币种状态
     * User: zhouying
     * Date: 2018/5/17
     */
    public function doChange(){
        $param = input('post.');
        if (!in_array($param['status'], [-1,1])){
            return SKReturn("非法请求!");
        }
        $coin_system = Db::name('coin_system')->where(['id' => $param['id'],'dataFlag'=>1])->field('coin,black')->find();
        if (empty($coin_system)){
            return SKReturn("该系统钱包不存在!");
        }
        if($param['status'] == 1){
            if($param['black'] > 100000000)return SKReturn("转入数量太大!");
            $data['black'] = Db::raw('black+'.$param['black']);
            $data['total'] = Db::raw('total+'.$param['black']);
            $sid = 21;
            $numType = 1;
            $orderNo = SKOrderSn('y');
            $remark = "后台系统钱包余额转入";
        }else if($param['status'] == -1){
            if($coin_system['black'] < $param['black'])return SKReturn("系统钱包余额不足!");
            $data['black'] = Db::raw('black-'.$param['black']);
            $sid = 22;
            $numType = -1;
            $orderNo = SKOrderSn('z');
            $remark = "后台系统钱包余额转出";
        }
        try{
            Db::name('log_coin_system')->insert([
                'coin' => $coin_system['coin'],
                'sid' => $sid,
                'orderNo' => $orderNo,
                'preNum' => $coin_system['black'],
                'numType' => $numType,
                'num' => $param['black'],
                'remark' => $remark,
                'createTime' => date("Y-m-d H:i:s")
            ]);
            Db::name('coin_system')->where(['id' => $param['id']])->update($data);
        }catch (\Exception $e){
            return SKReturn("操作失败!".$e->getMessage());
        }
        return SKReturn("操作成功!" ,1);

    }

    /**
     * 兑换管理
     * @return mixed|string
     * @author tiger
     * 2018-6-21
     */
    public function exchangePageQuery()
    {
        $where = [];
        if (input('userPhone')) {
            $where['u.userPhone'] = input('userPhone');
        }
        if (input('coin') && input('coin') != -1) {
            $where['l.coin'] = input('coin');
        }
        //起止时间
        if (input('startTime') || input('endTime')) {
            $where['l.createTime'] = timeTerm(input('startTime'), input('endTime'), 2);
        }
        $page = Db::name('coin_exchange')->alias('l')
            ->join('users u', 'u.userId=l.userId', 'left')
            ->field('u.userPhone,l.*')
            ->where($where)
            ->order('l.id', 'desc')
            ->paginate(input('limit/d'))
            ->toArray();
        return $page;
    }

    /**
     * 可划扣列表
     * @return mixed|string
     * @author tiger
     * 2018-6-21
     */
    public function delimitPageQuery()
    {
        $where = [];
        $DelimitNum = SysConfig("DelimitNum");
        $where['coinBalance'] = ['>=',$DelimitNum];
        if (input('userPhone')) {
            $where['u.userPhone'] = input('userPhone');
        }
        if (input('coin') && input('coin') != -1) {
            $where['l.coin'] = input('coin');
        }

        $page = Db::name('users_coin')->alias('l')
            ->join('users u', 'u.userId=l.userId', 'left')
            ->field('u.userPhone,l.platformAddr,l.coinBalance,l.coin,l.id,l.hasFee')
            ->where($where)
            ->order('l.id', 'desc')
            ->paginate(input('limit/d'))
            ->toArray();

        foreach($page['data'] as $k=>$v){
            $page['data'][$k]['hasFee'] = $this->hasFee[$v['hasFee']];
        }
        return $page;
    }

    /**
     * 不可划扣列表
     * @return mixed|string
     * @author tiger
     * 2018-6-21
     */
    public function notDelimitPageQuery()
    {
        $where = [];
        $DelimitNum = bcsub(SysConfig("DelimitNum"),1,4);
        $where['coinBalance'] = ['between',[1,$DelimitNum]];
        if (input('userPhone')) {
            $where['u.userPhone'] = input('userPhone');
        }
        if (input('coin') && input('coin') != -1) {
            $where['l.coin'] = input('coin');
        }
        //起止时间
        if (input('startTime') || input('endTime')) {
            $where['l.createTime'] = timeTerm(input('startTime'), input('endTime'), 2);
        }
        $page = Db::name('users_coin')->alias('l')
            ->join('users u', 'u.userId=l.userId', 'left')
            ->field('u.userPhone,l.platformAddr,l.coinBalance,l.coin')
            ->where($where)
            ->order('l.id', 'desc')
            ->paginate(input('limit/d'))
            ->toArray();
        return $page;
    }

    /**
     * 设置已转手续费字段状态
     * @return mixed|string
     * @author tiger
     * 2018-6-21
     */
    public function setStatus()
    {
        $data = input('post.');
        if(empty($data['id']))return SKReturn("参数错误");
        $users_coin = Db::name('users_coin')->where(['id'=>$data['id'],'hasFee'=>0])->field('id')->find();
        if(empty($users_coin)){
            return SKReturn("该数据不存在或已标记");
        }
        $result = Db::name('users_coin')->where(['id'=>$data['id']])->update(['hasFee'=>1]);
        if($result == true){
            return SKReturn("操作成功",1);
        }
        return SKReturn("操作失败");
    }

}