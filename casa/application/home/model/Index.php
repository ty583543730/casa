<?php

namespace app\home\model;

use think\Model;
use think\Db;

class Index extends Model
{
    /*获取banner*/
    public function banner($lang)
    {
        if ($lang == 'en-us') {
            $adPositionId = 5;
        } else {
            $adPositionId = 4;
        }
        return Db::name('ads')->where(['adPositionId' => $adPositionId, 'dataFlag' => 1])->field('adId,adFile')->select();
    }

    /*获取公告信息*/
    public function notice($lang)
    {
        if ($lang == 'en-us') {
            $data = Db::name('articles')->where(['catId' => 1, 'dataFlag' => 1, 'isShow' => 1])->field('articleId,articleTitleEn as articleTitle')->select();
        } else {
            $data = Db::name('articles')->where(['catId' => 1, 'dataFlag' => 1, 'isShow' => 1])->field('articleId,articleTitle')->select();
        }
        return $data;
    }

    /*获取公告信息分页*/
    public function noticePage($lang)
    {
        if ($lang == 'en-us') {
            $res = Db::name('articles')
                ->where(['catId' => 1, 'dataFlag' => 1, 'isShow' => 1, 'articleContentEn' => ['exp', Db::raw('is not null')]])
                ->field('articleId,articleTitleEn as articleTitle,createTime')
                ->order('articleId desc')
                ->paginate(10)
                ->toArray();
        } else {
            $res = Db::name('articles')
                ->where(['catId' => 1, 'dataFlag' => 1, 'isShow' => 1, 'articleContent' => ['exp', Db::raw('is not null')]])
                ->field('articleId,articleTitle,createTime')
                ->order('articleId desc')
                ->paginate(10)
                ->toArray();
        }
        return $res;
    }

    /*获取公告详情分页*/
    public function getInfo($lang)
    {
        $id = input('id');
        if (empty($id))
            return SKReturn('无此广告信息');
        if ($lang == 'en-us') {
            $res = Db::name('articles')->where('articleId', $id)->field('articleTitleEn as articleTitle,articleContentEn as articleContent,createTime')->find();
        } else {
            $res = Db::name('articles')->where('articleId', $id)->field('articleTitle,articleContent,createTime')->find();
        }
        $res['articleContent'] = htmlspecialchars_decode($res['articleContent']);
        return $res;
    }
}