<?php

/**
 * Class Config
 *
 * 执行Sample示例所需要的配置，用户在这里配置好Endpoint，AccessId， AccessKey和Sample示例操作的
 * bucket后，便可以直接运行RunAll.php, 运行所有的samples
 */
namespace OSS;

final class OssConfig
{
    const OSS_ACCESS_ID = 'LTAI7fWX4o5LUOOf';
    const OSS_ACCESS_KEY = 'lQWwhsb6tJAuKikZ0cK7puTwB5Jsa0';
    const OSS_ENDPOINT = 'oss-cn-shenzhen.aliyuncs.com';
    const OSS_FILE_BUCKET = 'yingmai-zhibo';//OssBucket名称
}
