<?php
/**
 * 基类
 * User: yt
 * Date: 2018/6/1 0001
 * Time: 下午 1:55
 */

namespace app\home\controller;


use think\Controller;
use think\Lang;

class Base extends Controller
{

    protected $lang;
    public function _initialize()
    {
        parent::_initialize();
        $this->lang = Lang::detect();
    }

    /*
     * 设置当前语言
     * @param lang zh-cn  en-us
    */
    public function setLang()
    {
        $lang = input('lang','');
        if(!empty($lang)){
            if(in_array($lang,config('lang_list'))){
                cookie('think_var',$lang);
                return 1;
            }
        }
        return 0;
    }

    /**
     * 获取验证码
     */
    public function getVerify()
    {
        getVerify();
    }
}