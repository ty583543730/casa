<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用行为扩展定义文件
return [
    // 模块初始化
    'module_init'  => [
        'app\\admin\\behavior\\InitConfig'
    ],
    // 操作开始执行
    'action_begin'=> [
        //'app\\admin\\behavior\\ListenLoginStatus',
        'app\\admin\\behavior\\ListenAction',
    ],
];
