<?php

namespace app\home\controller;

use app\home\model\Index as I;

class Index extends Base
{

    public function index()
    {
        //获取的当前语言
        $lang = $this->lang;
        $i = new I();
        //获取banner
        $banner = $i->banner($lang);
        //获取公告
        $notice = $i->notice($lang);
        //菜单导航
        $nav = ['home' => lang('home'),
            'project' => lang('project'),
            'advantage' => lang('advantage'),
            'aboutUs' => lang('about us'),
            'more' => lang('more'),
            'guide' => lang('guide'),
            'serviceAgreement' => lang('service agreement'),
            'contactUs' => lang('contact us'),
            'workday' => lang('workday'),
            'download' => lang('download')];
        //lnmi特点
        $lnmiTrait = [
            'international' => lang('international new'),
            'introduction' => lang('project introduction'),
            'info' => lang('lnmi word first block chain'),
            'token' => lang('introduces the INMC TOKEN'),
            'trait' => lang('lnmi characteristics'),
            'centralization' => lang('further decentralization'),
            'centralizationInfo' => lang('lnmi platform is a sustainable'),
            'Intelligence' => lang('intelligent interactions'),
            'IntelligenceInfo' => lang('everyone in the ecosystem'),
            'speedUp' => lang('speed up trading'),
            'speedUpInfo' => lang('the user helps the user'),
            'privacy' => lang('user secure'),
            'privacyInfo' => lang('the unique privacy protection'),
            'transaction' => lang('more secure'),
            'transactionInfo' => lang('INMC as key currency'),
            'download' => lang('click download')
        ];
        //我们的团队
        $team = [
            'ourTeam' => lang('Our team'),
            'ceo' => lang('CEO'),
            'ceoInfo' => lang('tom founded gocardless'),
            'coFounder' => lang('co - Founder'),
            'coFounderInfo' => lang('Ivan Moscow university'),
            'legalAdviser' => lang('legal adviser'),
            'legalAdviserInfo' => lang('gina have very good affinity'),
            'darrell' => lang('Darrell F.Bruce，HR'),
            'darrellInfo' => lang('darrell Cambridge university'),
        ];
        $this->assign('lang', $lang);
        $this->assign('nav', $nav);
        $this->assign('lnmiTrait', $lnmiTrait);
        $this->assign('team', $team);
        $this->assign('banner',$banner);
        $this->assign('notice',$notice);
        return $this->fetch('index');
    }

    /*语言切换*/
    public function lang()
    {
        switch (input('lang')) {
            case 'zh-cn':
                cookie('think_var', 'zh-cn');
                break;
            case 'en-us':
                cookie('think_var','en-us');
                break;
        }
    }

    public function list()
    {
        return $this->fetch('list');
    }

    public function noticePage(){
        $i = new I();
        $data = $i->noticePage($this->lang);
        return SKReturn('查询成功',1,$data);
    }

    public function info()
    {
        $i = new I();
        $data = $i->getInfo($this->lang);
        $this->assign('data',$data);
        return $this->fetch('info');
    }
}
