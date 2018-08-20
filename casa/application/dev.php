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
define('BASE_URL', 'http://casa.daliuliang.com.cn/');

//系统币种
define('SYSTEM_COIN', 'CASA');

//交易所
define('EXCHANGE_DOMAIN', 'http://zhaoxi.daliuliang.com.cn');
define('EXCHANGE_KEY', '6b8yz7');

return [

    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------
    'app_debug' => true,
    // 应用Trace
    'app_trace' => false,
    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session' => [
        'id' => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix' => 'think',
        // 驱动方式 支持redis memcache memcached
        'type' => 'redis',
        // 是否自动开启 SESSION
        'auto_start' => true,
        'host' => '115.28.128.58', // redis主机
        'port' => 6379, // redis端口
        'password' => 'dPeryV8G6vyAF3pW', // 密码
        'select' => 3, // 操作库
        'expire' => 3600*5, // 有效期(秒)
        'timeout' => 0, // 超时时间(秒)
        'persistent' => true, // 是否长连接
        'session_name' => 'yc_', // sessionkey前缀
        'sentinel' => false,//是否开启哨兵模式
    ],
    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------
    'cache' => [
        // 驱动方式
        'type' => 'redis',
        // 缓存保存目录
        'path' => CACHE_PATH,
        'host' => '115.28.128.58',
        'port' => 6379,
        'password' => 'dPeryV8G6vyAF3pW',
        'select' => 4,
        'timeout' => 0,
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
        'persistent' => false,
        // 缓存前缀
        'prefix' => 'ym_',
        'sentinel' => false,//是否开启哨兵模式
    ],
    //数据库
    'database' => [
        // 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => 'rm-m5e08873yn656m1y8o.mysql.rds.aliyuncs.com',
        // 数据库名
        'database' => 'casa',
        // 用户名
        'username' => 'casa',
        // 密码
        'password' => 'VNTLT9zINWxmmTdd',
        // 端口
        'hostport' => '',
        // 连接dsn
        'dsn' => '',
        // 数据库连接参数
        'params' => [],
        // 数据库编码默认采用utf8
        'charset' => 'utf8',
        // 数据库表前缀
        'prefix' => 'sk_',
        // 数据库调试模式
        'debug' => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => false,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
        // 是否严格检查字段是否存在
        'fields_strict' => true,
        // 数据集返回类型
        'resultset_type' => 'array',
        // 自动写入时间戳字段
        'auto_timestamp' => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain' => false,
    ],
    // +----------------------------------------------------------------------
    // | 消息服务
    // +----------------------------------------------------------------------
    'queue' => [
        'connector' => 'redis',
        'expire' => 60,
        'default' => 'default',
        'host' => '115.28.128.58',
        'port' => 6379,
        'password' => 'dPeryV8G6vyAF3pW',
        'select' => 5,
        'timeout' => 0,
        'persistent' => false
    ],
    // +----------------------------------------------------------------------
    // | redis Native
    // +----------------------------------------------------------------------
    'redis' => [
        'host' => '115.28.128.58',
        'port' => 6379,
        'auth' => 'dPeryV8G6vyAF3pW',
        'select' => 5,
        'timeout' => 0
    ],


];
