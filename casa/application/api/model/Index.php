<?php
/**
 * 首页数据
 * User: Administrator
 * Date: 2018/8/13
 * Time: 10:55
 */

namespace app\api\model;


use think\Db;

class Index extends Base
{
    /*未登录的数据图片，公告数据*/
    public function index()
    {
        $data=cache('app_index_nologin_data');
        if(empty($data)){
            $data['ads'] = Db::name("ads")->field("adFile,adName,adURL")->where(['adPositionId' => 1, 'dataFlag' => 1])->order("adSort asc")->select();
            $data['notice'] = Db::name("articles")->field("logo,articleId,articleTitle,num,createTime")->where(['catId' => 1, 'isShow' => 1, 'dataFlag' => 1])->order("articleId desc")->limit(4)->select();
            $data['pop-ups'] = '特殊弹窗';
            cache('app_index_nologin_data',$data,300);
        }
        return SKReturn('获取成功',1,$data);
    }
}