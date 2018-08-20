<?php
/**
 * 数字币手动充值
 * User: tiger
 * Date: 2018/5/17
 * Time: 11:37
 */

namespace app\admin\model;
use think\Db;

class ManualReharge extends Base
{
    /**
     * 检测用户是否存在
     * User: tiger
     */
    public function checkUser()
    {
        $param = input('post.');
        $user = Db::name('users')->where(['userPhone' => $param['userPhone']])->field('userPhone')->find();
        if (empty($user)){
            return SKReturn("不存在此用户!");
        }
        return SKReturn("校验通过!",1);
    }

    /**
     * 数字币手动充值
     * User: tiger
     * Date: 2018/7/02
     */
    public function manualRecharge(){
        $param = input('post.');

        $must = ["coin", "num","userPhone","status"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }

        if ($param['num'] > 1000000) {
            return SKReturn("最大充值额度为1000000");
        }
        $user = Db::name('users u')
            ->join('users_coin uc','u.userId=uc.userId','left')
            ->where(['userPhone' => $param['userPhone'],'uc.coin'=>$param['coin']])
            ->field('u.userId,uc.forzen,uc.black,uc.locker')
            ->find();
        if (empty($user)){
            return SKReturn("不存在此用户!");
        }
        switch ($param['status']){
            case 1: //增加币
                if (time() <= strtotime(SysConfig('privatePlacementDate'))) {
                    $locker = '+'.$param['num'];
                    $black = 0;
                    $users_coin['locker'] = Db::raw('locker+'.$param['num']);
                    $users_coin['rechargeTotal'] = Db::raw('rechargeTotal+'.$param['num']);
                }else{
                    $locker = 0;
                    $black = '+'.$param['num'];
                    $users_coin['black'] = Db::raw('black+'.$param['num']);
                    $users_coin['rechargeTotal'] = Db::raw('rechargeTotal+'.$param['num']);
                }
                $type = 44;
                $remark = "数字币手动充值";
                break;
            case -1: //减少币
                if(time() <= strtotime(SysConfig('privatePlacementDate'))){
                    if($user['locker'] < $param['num'])return SKReturn("该用户锁定币不足!");
                    $locker = '-'.$param['num'];
                    $black = 0;
                    $users_coin['locker'] = Db::raw('locker-'.$param['num']);
                }else{
                    if($user['black'] < $param['num'])return SKReturn("该用户可用币不足!");
                    $locker = 0;
                    $black = '-'.$param['num'];
                    $users_coin['black'] = Db::raw('black-'.$param['num']);
                }
                $type = 45;
                $remark = "数字币手动减少";
                break;
        }

        Db::startTrans();
        try{
            $orderNo = SKOrderSn('a');
            Db::name('users_coin')->where(['userId' => $user['userId'],'coin'=>$param['coin']])->update($users_coin);
            insertLog($param['coin'],$user['userId'],$orderNo,$type,$black,$locker,0.0000,$remark);
            Db::commit();
            return SKReturn("操作成功!" ,1);
        }catch (\Exception $e){
            Db::rollback();
            return SKReturn("操作失败!");
        }
    }
}