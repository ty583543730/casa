<?php

use think\Db;
use think\Log;

/**
 * 获取验证码
 */
function getVerify()
{
    $Verify = new \verify\Verify();
    $Verify->length = 4;
    $Verify->entry();
}

/**
 * 核对验证码
 */
function checkVerify($code)
{
    $verify = new \verify\Verify();
    return $verify->check($code);
}

// 应用公共文件
/**
 * 获取系统配置数据
 */
function SysConfig($fieldCode = false)
{
    $rs = cache('SysConfig');
    if (!$rs) {
        $rv = Db::name('sys_configs')->field('fieldCode,fieldValue')->select();
        $rs = [];
        foreach ($rv as $v) {
            $rs[$v['fieldCode']] = $v['fieldValue'];
        }
        cache('SysConfig', $rs, 86400, "clear");
    }
    if ($fieldCode) {
        $rs = $rs[$fieldCode];
    }
    return $rs;
}

/**
 * 防并发处理
 * @param string $code 业务名称
 * @param integer $uid 用户id
 * @param integer $remove 0：插入 1：删除标志
 * @return integer 0为插入或删除失败 成功返回大于0的数字
 */
function optBing($code, $uid, $remove = 1)
{
    if (config("app_debug")) {
        return 1;
    } else {
        $redis = new \redis\Native();
        if ($remove) {
            return $redis->srem('optBing', $code . '_' . $uid);
        } else {
            return $redis->sadd('optBing', $code . '_' . $uid);
        }
    }
}

/**
 * 生成数据返回值
 */
function SKReturn($msg, $status = -8, $data = [])
{
    $rs = ['status' => $status, 'msg' => $msg];
    if (!empty($data)) {
        $rs['data'] = $data;
    } else {
        $rs['data'] = array();
    }
    return $rs;
}

/**
 * LAYUI的接口返回
 */
function PQReturn($data = [], $code = 0, $msg = '获取成功')
{
    $rs = ['code' => $code, 'msg' => $msg];
    if (isset($data['total'])) $rs['count'] = $data['total'];
    if (isset($data['data'])) $rs['data'] = $data['data'];
    return $rs;
}

/**
 * 获取流水业务列表
 * @param $tableName  流水表名称 去掉前缀
 * @param $flag       是否转为一维数组  key为 tCode value为tName
 */
function getLogTradeList($tableName, $flag = true)
{
    $type = $flag == true ? 1 : 0;
    $result = cache("dict_" . $type . $tableName);
    if (empty($result)) {
        $where['dType'] = $tableName;
        $where['dataFlag'] = 1;
        $result = Db::name('data_dict')->where($where)->field('dataFlag,createTime,sort', true)->order('sort desc')->select();
        if ($flag) {
            $result = array_column($result, 'dName', 'dCode');
        }
        cache("dict_" . $type . $tableName, $result, 120);
    }
    return $result;
}

/**
 * 获取币币交易支持的币种列表
 * @return Array
 */
function SKBtcCoin()
{
    $coinList = cache('BTC_Trade_Coin');
    if (empty($coinList)) {
        $coinList = Db::name('coin')->where(['status' => 1, 'dataFlag' => 1])->field('id,coin,title')->select();
        cache('BTC_Trade_Coin', $coinList);
    }
    return $coinList;
}

/**
 * 删除一维数组里的多个key
 */
function SKUnset(&$data, $keys)
{
    if ($keys != '' && is_array($data)) {
        $key = explode(',', $keys);
        foreach ($key as $v) unset($data[$v]);
    }
}

/**
 * 时间区域搜索条件（字段为int）
 * @param $start 开始时间
 * @param $end   结束时间
 * @param $type   查询类型（1：int；2：DATETIME;）
 * @return array
 */
function timeTerm($start, $end, $type = 2)
{
    if ($type == 2) {
        if (!empty($start) && !empty($end)) {
            $where = ['between', [$start . " 00:00:00", $end . " 23:59:59"]];
        } elseif (!empty($start)) {
            $where = ['>=', $start . " 00:00:00"];
        } elseif (!empty($end)) {
            $where = ['<=', $end . " 23:59:59"];
        } else {
            $where = false;
        }
    } else if ($type == 1) {
        if (!empty($start) && !empty($end)) {
            $where = ['between', [date('Ymd', strtotime($start . " 00:00:00")), date('Ymd', strtotime($end . " 23:59:59"))]];
        } elseif (!empty($start)) {
            $where = ['>=', strtotime($start . " 00:00:00")];
        } elseif (!empty($end)) {
            $where = ['<=', strtotime($end . " 23:59:59")];
        } else {
            $where = false;
        }
    } else {
        if (!empty($start) && !empty($param['endDate'])) {
            $where = ['between', [strtotime($start . " 00:00:00"), strtotime($end . " 23:59:59")]];
        } elseif (!empty($start)) {
            $where = ['>=', strtotime($start . " 00:00:00")];
        } elseif (!empty($param['endDate'])) {
            $where = ['<=', strtotime($end . " 23:59:59")];
        } else {
            $where = false;
        }
    }
    return $where;
}

/**
 * 判断手机号格式是否正确
 */
function SKIsPhone($phoneNo)
{
    $reg = "/^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,3,6,7,8]{1}\d{8}$|^18[\d]{9}$/";
    $rs = \think\Validate::regex($phoneNo, $reg);
    return $rs;
}

/**
 * 判断手机号格式是否正确
 */
function SKIsEmail($email)
{
    $reg = "/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/";
    $rs = \think\Validate::regex($email, $reg);
    return $rs;
}

/**
 * 判断用户名格式是否正确
 */
function SKIsName($name)
{

//    $reg = "/^[a-zA-Z][a-zA-Z0-9_]{1,20}$/";
    $reg = "/^\d{1,20}$/";
    $rs = \think\Validate::regex($name, $reg);
    if ($rs) {
        return SKReturn("name cannot be pure numbers", 1);
    }
    return SKReturn("");
}

/**
 * 判断密码格式是否正确
 * @param  $password    密码
 * @param  $type    类型（1：登录密码；2：交易密码）
 */
function SKIsPwd($password, $type = 1)
{
    if ($type == 1) {
        $reg = "/^[0-9a-zA-Z!@#$%^&*]{6,20}$/";
    } elseif ($type == 2) {
        $reg = "/^\d{6}$/";
    }
    $rs = \think\Validate::regex($password, $reg);
    return $rs;
}

/**
 * 获取订单统一流水号（主要使用）
 */
function SKOrderSn($pref = '')
{
    mt_srand((double)microtime() * 1000000);
    return $pref . date("YmdHis") . str_pad(mt_rand(1, 99999), 5, "0", STR_PAD_LEFT);
}

/**
 * 用于生成子单号
 */
function SKOrderNo()
{
    $orderId = Db::name('orderids')->insertGetId(['rnd' => time()]);
    return $orderId . (fmod($orderId, 7));
}

/*
 * 校验支付密码/登录密码
 * @param $userId    用户ID
 * @param $password   支付密码/登录密码
 * @return $field   支付密码/登录密码的字段名
 */
function checkPwd($userId, $password, $field)
{
    $user = Db::name('users')->field("loginSecret,loginPwd,payPwd")->where('userId', $userId)->find();
    if (!empty($user)) {
        if (empty($user['payPwd']) && $field == "payPwd") {
            return SKReturn("请设置交易密码", -7);
        }
    }
    if (md5($password . $user["loginSecret"]) == $user[$field]) {
        return SKReturn("", 1);
    } else {
        if ($field == "loginPwd") {
            $msg = "登录密码错误";
        } else {
            $msg = "交易密码错误";
        }
        return SKReturn($msg, -1);
    }
}

/**
 * 时间格式
 * @param $type    时间格式类型
 */
function timeFormat($type = 1)
{
    $result = "";
    switch ($type) {
        case 1:
            $result = date("Y-m-d H:i:s");
            break;
        case 2:
            $result = date("Y-m-d");
            break;
    }
    return $result;
}

/**
 * 身份证号码验证（真正要调用的方法）
 * @param $id_card   身份证号码
 */
function validation_filter_id_card($id_card)
{
    if (strlen($id_card) == 18) {
        $idcard_base = substr($id_card, 0, 17);
        if (idcard_verify_number($idcard_base) != strtoupper(substr($id_card, 17, 1))) {
            return false;
        } else {
            return true;
        }
    } elseif ((strlen($id_card) == 15)) {
        // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
        if (array_search(substr($id_card, 12, 3), array('996', '997', '998', '999')) !== false) {
            $idcard = substr($id_card, 0, 6) . '18' . substr($id_card, 6, 9);
        } else {
            $idcard = substr($id_card, 0, 6) . '19' . substr($id_card, 6, 9);
        }
        $idcard = $idcard . idcard_verify_number($idcard);
        if (strlen($idcard) != 18) {
            return false;
        }
        $idcard_base = substr($idcard, 0, 17);
        if (idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
            return false;
        } else {
            return true;
        }
    } else {
        return false;
    }
}

/**
 * 计算身份证校验码，根据国家标准GB 11643-1999
 * @param $idcard_base   身份证号码
 */
function idcard_verify_number($idcard_base)
{
    if (strlen($idcard_base) != 17) {
        return false;
    }
    //加权因子
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
    //校验码对应值
    $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
    $checksum = 0;
    for ($i = 0; $i < strlen($idcard_base); $i++) {
        $checksum += substr($idcard_base, $i, 1) * $factor[$i];
    }
    $mod = $checksum % 11;
    $verify_number = $verify_number_list[$mod];
    return $verify_number;
}

/**
 * 生成JSON数据返回值
 */
function JSONReturn($result)
{
    return json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

/**
 * 输出并打断
 * @return mix
 */
function dd($param)
{
    dump($param);
    die;
}

/**
 * 数组过滤
 * @param mix
 * @return mix
 */
function trimArray($param)
{
    if (!is_array($param)) {
        return trim($param);
    }
    return array_map('trimArray', $param);
}

/**
 * 必须参数验证
 * @param $must     必须参数数组
 * @param $params    被验证的数组
 * @return boolean
 */
function mustParams($must, $params)
{
    foreach ($must as $v) {
        if (empty($params[$v])) {
            return false;
        }
    }
    return true;
}

/**
 * 隐藏手机号码
 */
function phoneHide($phone)
{
    $phone = substr($phone, 0, 3) . "****" . substr($phone, -4);
    return $phone;
}

/**
 * 隐藏身份证号码
 */
function cardHide($cardId)
{
    $cardId = substr($cardId, 0, 4) . "****" . substr($cardId, -4);
    return $cardId;
}

/**
 * 插入日志的公共方法
 * @param $coin     币种
 * @param $uid    用户id
 * @param $orderNo    订单编号
 * @param $type    业务类型
 * @param $Black    可用币 +号获得，-号支出
 * @param $Lock    锁仓币
 * @param $Frozen    冻结币
 * @param $remark    备注
 * @return boolean
 */
function insertLog($coinName, $uid, $orderNo, $type, $Black = 0.0000, $Lock = 0.0000, $Frozen = 0.0000, $remark = '', $createTime = "")
{
    $coinName = strtoupper($coinName);
    $coin = Db::name('users_coin')->where(['userId' => $uid, 'coin' => $coinName])->field('black,locker,forzen')->find();
    if (is_null($coin['black'])) {
        throw new Exception('收款人账户不存在');
    }
    $insertLogData = [
        'userId' => $uid,
        'orderNo' => $orderNo,
        'type' => $type,
        'preCoin' => $coin['black'],
        'preCoinLock' => $coin['locker'],
        'preCoinFrozen' => $coin['forzen'],
        'coinBlack' => $Black,
        'coinLock' => $Lock,
        'coinFrozen' => $Frozen,
        'remark' => $remark,
        'createTime' => empty($createTime) ? $createTime : date("Y-m-d H:i:s")
    ];
    Db::name('log_' . strtolower($coinName))->insert($insertLogData);
}


function moneyFormat($money)
{
    return floatval($money);
}

/**
 * @param int $left 左操作数
 * @param int $right 右操作数
 * @param int $scale 设置结果中小数点后的小数位数(没有四舍五入)
 * @param $type 使用类型(1：加；2：减；3：乘；4：除；5：求余；6：次方；7：求平方根；8：给所有函数设置小数位精度；9：比较；)
 * @return string
 */
function bcFunc($left = 0, $right = 0, $scale = 4, $type)
{
    switch ($type) {
        case 1:
            $result = bcadd($left, $right, $scale);
            break;
        case 2:
            $result = bcsub($left, $right, $scale);
            break;
        case 3:
            $result = bcmul($left, $right, $scale);
            break;
        case 4:
            $result = bcdiv($left, $right, $scale);
            break;
        case 5:
            $result = bcmod($left, $right);
            break;
        case 6:
            $result = bcpow($left, $right, $scale);
            break;
        case 7:
            $result = bcsqrt($left, $scale);
            break;
        case 8:
            $result = bcscale($scale);
            break;
        case 9:
            $result = bccomp($left, $right, $scale);
            break;
    }
    return $result;
}

/**
 * 日期的时间间隔
 * @param type $type
 * @return type
 */
function getDateTime($type = "week")
{
    $limit = array();
    switch ($type) {
        case "today":
            $start = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
            $end = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d"), date("Y")));
            $limit = [$start, $end];
            break;
        case "yesterday":
            $start = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
            $end = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d") - 1, date("Y")));
            $limit = [$start, $end];
            break;
        case "week":
            $start = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - 6, date("Y")));
            $end = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d"), date("Y")));
            $limit = [$start, $end];
            break;
        case "month":
            $start = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") - 1, date("d"), date("Y")));
            $end = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d"), date("Y")));
            $limit = [$start, $end];
            break;
        case "3month":
            $start = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") - 3, date("d"), date("Y")));
            $end = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d"), date("Y")));
            $limit = [$start, $end];
            break;
        case "4month":
            $start = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") - 4, date("d"), date("Y")));
            $end = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d"), date("Y")));
            $limit = [$start, $end];
            break;
        case "6month":
            $start = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") - 6, date("d"), date("Y")));
            $end = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d"), date("Y")));
            $limit = [$start, $end];
            break;
    }
    return $limit;
}

/**
 * 导出excel表格
 * @param array $date 表格数据
 * @param array $title 表格表头
 * @param string $savefile 生成表格名
 */
function exportExcel($data = array(), $title = array(), $savefile = null)
{
    if (count($data) < 50000) {
        header("Content-type: text/html; charset=UTF-8");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=" . $savefile . ".xls");
        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        $html = "<HTML>";
        $html .= "<HEAD>";
        $html .= "<META http-equiv=Content-Type content=\"text/html; charset=utf-8\">";
        $html .= "</HEAD>";
        $html .= "<BODY>";
        $html .= "<TABLE BORDER=1>";
        $html .= '<tr><td style="width: 500px;font-size: 20px;" colspan = "9" align="center" valign="middle">' . $savefile . '</td></tr>';
        $html .= '<tr>';
        foreach ($title as $v) {
            $html .= "<td style='width:150px'>{$v}</td>";
        }
        $html .= '</tr>';
        foreach ((array)$data as $r => $value) {
            $html .= '<tr>';
            foreach ((array)$value as $c => $v) {
                if (strripos($v, '.') > 0) {
                    $html .= "<td style='text-align:right;' >" . $v . "</td>";
                } else {
                    $html .= "<td style='vnd.ms-excel.numberformat:@' >" . " " . $v . "</td>";
                }
            }
            $html .= '</tr>';
        }
        $html .= "</TABLE>";
        $html .= "</BODY>";
        $html .= "</HTML>";
        echo $html;
        exit;
    } else {
        $savefile = $savefile . ".xls";
        $zipfile = "td" . date("YmdHis") . ".zip";
        copy("tongdui.zip", $zipfile);
        chmod($zipfile, 0777);

        $zip = new \ZipArchive;
        if ($zip->open($zipfile, ZipArchive::CREATE) !== TRUE) {
            exit('无法打开文件，或者文件创建失败');
        }

        @ini_set('memory_limit', '1000M');//设置一下使用内存，由于数据量很大，不设置不行，默认的不够
        @ini_set('output_buffering', 'On');//设置 一下缓冲区占用的内存，默认的依然不够用
        set_time_limit(1000);//设置一下执行时间，同理，默认时间不够用
        ob_start();//缓冲区开始
        $html = "<HTML>";
        $html .= "<HEAD>";
        $html .= "<META http-equiv=Content-Type content=\"text/html; charset=utf-8\">";
        $html .= "</HEAD>";
        $html .= "<BODY>";
        $html .= "<TABLE BORDER=1>";
        $html .= '';
        $html .= '<tr><td style="width: 500px;font-size: 20px;" colspan = "9" align="center" valign="middle">' . $savefile . '</td></tr>';
        foreach ($title as $v) {
            $html .= "<td style='width:150px'>{$v}</td>";
        }
        $html .= '</tr>';
        foreach ((array)$data as $r => $value) {
            $html .= '<tr>';
            foreach ((array)$value as $c => $v) {
                if (strripos($v, '.') > 0) {
                    $html .= "<td style='text-align:right;' >" . $v . "</td>";
                } else {
                    $html .= "<td style='vnd.ms-excel.numberformat:@' >" . " " . $v . "</td>";
                }
            }
            $html .= '</tr>';
        }
        $html .= "</TABLE>";
        $html .= "</BODY>";
        $html .= "</HTML>";
        file_put_contents($savefile, $html);
        if ($zip->open($zipfile, ZipArchive::OVERWRITE) === TRUE) {
            $zip->addFile($savefile);
            $zip->close();
            unlink($savefile);
        }

        header("Content-Type:text/html;charset=utf-8;application/zip");//不用说了，谁都知道啥意思，不知道的自己搜
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename=' . $zipfile); //文件名
        header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
        header('Content-Length: ' . filesize($zipfile)); //告诉浏览器，文件大小
        @readfile($zipfile);
        exit;
    }

}

/**
 * 当前时间转换成UTC格式(UTC时区默认比北京时间偏移量少八个小时)
 * User: tiger
 * $dateTime "2018-06-14T02:27:30Z"
 * Date: 2018/6/14
 */
function asUtc($dateTime)
{
    $times = strtotime($dateTime);
    $time = $times - 8 * 3600;
    return date('Y-m-d\TH:i:s\Z', $time);
}

/**
 * 当前UTC格式时间转换成北京时间(UTC时区默认比北京时间偏移量少八个小时)
 * User: tiger
 * $utcTime "2018-06-14T02:27:30Z"
 * Date: 2018/6/14
 */
function asPrc($utcTime)
{
    $time = strtotime($utcTime);
    return date('Y-m-d h:i:s', $time);
}

/**
 * 计算两个时间之间相差多少时分秒
 * User: tiger
 * $utcTime "2018-06-14T02:27:30Z"
 * Date: 2018/6/14
 */
function time_diff($timestamp1, $timestamp2)
{
    if ($timestamp2 <= $timestamp1) {
        return ['hours' => 0, 'minutes' => 0, 'seconds' => 0];
    }
    $timediff = $timestamp2 - $timestamp1;
    $day = floor($timediff / (3600 * 24));
    $timediff = $timediff % (3600 * 24);//除去整天之后剩余的时间
    $hour = floor($timediff / 3600);
    $timediff = $timediff % 3600;//除去整小时之后剩余的时间
    $minute = floor($timediff / 60);
    $timediff = $timediff % 60;//除去整分钟之后剩余的时间
    $res = [];
    $res['day'] = $day;
    $res['hour'] = $hour;
    $res['minute'] = $minute;
    $res['timediff'] = $timediff;
    return $res;
}

/**
 * 接口校验数据签名
 * @return true/exit
 */
function ValidateSign($data)
{
    if (empty($data['rtime']) || empty($data['sign'])) {
        return SKReturn('缺少必要参数');
    }
    $make_sign = MakeSign($data);
    if ($make_sign !== $data['sign']) {
        return SKReturn('校验签名失败');
    }
    return true;
}

/**
 * 接口生成签名
 * @param $data
 * @param $key
 * @return string
 */
function MakeSign($data)
{
    $str = '';
    ksort($data);
    foreach ($data as $k => $v) {
        if ('sign' !== $k && '' !== $v) {
            $str .= $k . '=' . $v . '&';
        }
    }
    $str .= 'key=' . config('rpc_sign_key');
    $sign = md5($str);
    return $sign;
}

//加密 $data 要加密的字串 $key 盐
function SKencrypt($data, $key = "sk123")
{
    $char = '';
    $str = '';
    $key = md5($key);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) {
            $x = 0;
        }
        $char .= $key{$x};
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
    }
    return base64_encode($str);
}

//解密 $data 要解密的秘钥 $key 盐
function SKdecrypt($data, $key = "sk123")
{
    $char = '';
    $str = '';
    $key = md5($key);
    $x = 0;
    $data = base64_decode($data);
    $len = strlen($data);
    $l = strlen($key);
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) {
            $x = 0;
        }
        $char .= substr($key, $x, 1);
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return $str;
}

//格式化保留四位有效小数
function FormatNum($num)
{
    $num = number_format($num, 4, '.', '');
    return $num;
}

//获取所有国别列表
function getCountry()
{
    $country = Db::name('country')->where(['status' => 1, 'dataFlag' => 1])->field('id,cnName,number')->select();
    return $country;
}

/*二维数组按照指定的键值进行排序*/
function array_sort($arr, $keys, $type = 'asc')
{
    $sort = $old = $new = [];
    foreach ($arr as $k => $v) {
        $sort[$k] = $v[$keys];
        $old[$v[$keys]] = $v;
    }
    if ($type == 'asc') {
        sort($sort);
    } else {
        rsort($sort);
    }
    reset($sort);
    foreach ($sort as $v) {
        $new[] = $old[$v];
    }
    return $new;
}

/**
 * curl发送HTTP请求方法
 * @param  string $url 请求URL
 * @param  array $param GET参数数组
 * @param  string $data POST的数据，GET请求时该参数无效
 * @param  string $method 请求方法GET/POST
 * @param  string 日志名称
 * @return array          响应数据
 */
function curl_request($url, $param = [], $data = "", $method = 'POST', $log_name = "curl_request")
{
    Log::logger("请求数据", 'url:' . $url . ' param:' . JSONReturn($param) . ' data:' . JSONReturn($data), $log_name);
    $opts = array(
        CURLOPT_TIMEOUT => 300,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => false,    // 跳过证书检查
        CURLOPT_SSL_VERIFYHOST => false,    // 从证书中检查SSL加密算法是否存在
    );
    /* 根据请求类型设置特定参数 */
    if (count($param) > 0) {
        $opts[CURLOPT_URL] = $url . '?' . http_build_query($param);
    } else {
        $opts[CURLOPT_URL] = $url;
    }
    if (strtoupper($method) == 'POST') {
        $opts[CURLOPT_POST] = 1;
        $data = urlencode($data);
        $opts[CURLOPT_POSTFIELDS] = $data;
        if (is_string($data)) { //发送JSON数据
            $opts[CURLOPT_HTTPHEADER] = array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($data),
            );
        }
    }
    /* 初始化并执行curl请求 */
    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $result = curl_exec($ch);
    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($result == null) {
        $errInfo = "call http err :" . curl_errno($ch) . " - " . curl_error($ch);
        Log::logger("请求错误1_返回内容为NULL", JSONReturn($errInfo), $log_name);
        curl_close($ch);
        return "fail";
    } elseif ($responseCode != "200") {
        $errInfo = "call http err httpcode=" . $responseCode;
        Log::logger("请求错误2_响应字段不是200", JSONReturn($errInfo), $log_name);
        curl_close($ch);
        return "fail";
    }
    curl_close($ch);
    Log::logger("响应数据", JSONReturn($result), $log_name);
    return $result;
}

/**
 * 获取客户端ip地址
 * @return ip
 */
function get_real_ip()
{
    $ip = false;
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
        if ($ip) {
            array_unshift($ips, $ip);
            $ip = FALSE;
        }
        for ($i = 0; $i < count($ips); $i++) {
            preg_match('^(10│172.16│192.168).', $ips[$i],$matches);
            if (!empty($matches)) {
                $ip = $ips[$i];
                break;
            }
        }
    }
    return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}

/**
 * 上传图片
 * @param string $dir 目录
 * @param string $source 来源
 * @return array|string
 */
function uploadPic($dir = 'users', $source = 'layui')
{
    $fileKey = key($_FILES);
    $dir = Input('param.dir') ? Input('param.dir') : $dir;

    if ($dir == '') return json_encode(['msg' => '没有指定文件目录！', 'status' => -1]);
    $dirs = SysConfig("skUploads");
    $dirs = explode(',', trim($dirs, ','));
    if (!in_array($dir, $dirs)) {
        return json_encode(['msg' => '非法文件目录！', 'status' => -1]);
    }
    // 上传文件
    $file = request()->file($fileKey);
    if ($file === null) {
        return json_encode(['msg' => '上传文件不存在或超过服务器限制', 'status' => -1]);
    }
    $validate = new \think\Validate([
        ['fileMime', 'fileMime:image/png,image/gif,image/jpeg,image/x-ms-bmp', '只允许上传jpg,gif,png,bmp类型的文件'],
        ['fileExt', 'fileExt:jpg,jpeg,gif,png,bmp', '只允许上传后缀为jpg,gif,png,bmp的文件'],
        ['fileSize', 'fileSize:5242880', '文件大小超出限制'],//最大5M
    ]);
    $data = ['fileMime' => $file,
        'fileSize' => $file,
        'fileExt' => $file
    ];
    if (!$validate->check($data)) {
        return json_encode(['msg' => $validate->getError(), 'status' => -1]);
    }
    $info = $file->rule('uniqid')->move(ROOT_PATH . '/public/upload/' . $dir . "/" . date('Y-m'));
    if ($info) {
        $filePath = $info->getPathname();
        $filePath = str_replace(ROOT_PATH . '/public', '', $filePath);
        $filePath = str_replace('\\', '/', $filePath);
        $filePath = BASE_URL . ltrim($filePath, '/');
        if ($source == 'layui') {
            return PQReturn(['data' => ['src' => $filePath]]);
        } else {
            return SKReturn('上传成功', 1, ['src' => $filePath]);
        }
    } else {
        //上传失败获取错误信息
        return SKReturn('上传失败：' . $file->getError(), -8);
    }
}

function sendSms()
{
    $phone = 13612996757;
    $hasSend = cache($phone);
    if ($hasSend == false) {
        cache($phone, $phone, 300);
        $s = new \Sms\Sms();
        $s->sendSms($phone, $function = "register", $userId = 0, $phoneArea = 86, $type = 1);
    }
}

/**
 * 271奖励 批量处理
 * @param $params 待奖励数组 二位数组 ['type','orderNo','userId','total', 'ratio','num']
 * @return bool
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function BatchRewards($params)
{
    $forNum = ceil(count($params) / 500);

    for ($i = 1; $i <= $forNum; $i++) {
        debug('begin');
        //取出500条，并从数组中删除
        $RewardsData = array_splice($params, 0, 500, true);
        //初始化几个数组
        $Rewards = $Logscore = $LogCoin = $ExtendData = $userCoin = [];

        foreach ($RewardsData as $v) {
            $binding = bcmul($v['num'], SysConfig('rewardsBinding'), 4);
            $score = bcmul($v['num'], SysConfig('rewardsScore'), 4);
            $locker = bcmul($v['num'], SysConfig('rewardsLock'), 4);
            $userId = $v['userId'];
            $orderNo = $v['orderNo'];

            //查询用户扩展表获得用户积分
            $extend = Db::name('users_extend')->where(['userId' => $userId])->field('num,binding,totalNum,totalBinding')->find();
            if (empty($extend)) {
                Log::logger('error', '用户' . $userId . '用户扩展信息不存在', 'rewards');
                continue;
            }
            //用户账户
            $coin = Db::name('users_coin')->where(['userId' => $userId, 'coin' => 'CASA'])->field('black,locker,forzen')->find();
            if (is_null($coin['black'])) {
                Log::logger('error', '用户' . $userId . '用户账户不存在', 'rewards');
                continue;
            }

            //备注
            $type = $v['type'];
            $data_dict = getLogTradeList('rewards');
            $remark = $data_dict[$type];

            //奖励表数据
            $Rewards[] = [
                'type' => $type,
                'orderNo' => $orderNo,
                'userId' => $userId,
                'total' => $v['total'],
                'ratio' => $v['ratio'],
                'num' => $v['num'],
                'score' => $score,
                'binding' => $binding,
                'locker' => $locker,
                'createTime' => date("Y-m-d H:i:s")
            ];

            //绑定积分流水
            if ($binding > 0) {
                $Logscore[] = [
                    'userId' => $userId,
                    'orderNo' => $orderNo,
                    'type' => 4,
                    'preNum' => $extend['num'],
                    'preBinding' => $extend['binding'],
                    'num' => 0,
                    'binding' => $binding,
                    'remark' => $remark . '奖励绑定积分',
                    'createTime' => date("Y-m-d H:i:s")
                ];
            }
            //可用积分流水
            if ($score > 0) {
                $Logscore[] = [
                    'userId' => $userId,
                    'orderNo' => $orderNo,
                    'type' => 3,
                    'preNum' => $extend['num'],
                    'preBinding' => bcadd($extend['binding'], $binding, 4),
                    'num' => $score,
                    'binding' => 0,
                    'remark' => $remark . '奖励可用积分',
                    'createTime' => date("Y-m-d H:i:s")
                ];
            }
            //账户扩展变化
            if (!empty($Logscore)) {
                $ExtendData[] = [
                    $userId,
                    bcadd($extend['binding'], $binding, 4),
                    bcadd($extend['totalBinding'], $binding, 4),
                    bcadd($extend['num'], $score, 4),
                    bcadd($extend['totalNum'], $score, 4)
                ];
            }
            if ($locker > 0) {
                //币的流水
                $LogCoin[] = [
                    'userId' => $userId,
                    'orderNo' => $orderNo,
                    'type' => 14,
                    'preCoin' => $coin['black'],
                    'preCoinLock' => $coin['locker'],
                    'preCoinFrozen' => $coin['forzen'],
                    'coinBlack' => 0,
                    'coinLock' => $locker,
                    'coinFrozen' => 0,
                    'remark' => $remark . '奖励锁仓币',
                    'createTime' => date("Y-m-d H:i:s")
                ];
                //账户扩展变化
                $userCoin[] = [$userId, 'INMC', bcadd($coin['locker'], $locker, 4)];
            }
        }

        if (!empty($Rewards)) {
            Db::name('rewards')->insertAll($Rewards);
        }
        if (!empty($Logscore)) {
            Db::name('log_score')->insertAll($Logscore);
            BatchUpdate('sk_users_extend', ['userId', 'binding', 'totalBinding', 'num', 'totalNum'], $ExtendData);
        }
        if (empty($LogCoin)) {
            Db::name('log_casa')->insertAll($LogCoin);
            BatchUpdate('sk_users_coin', ['userId', 'coin', 'locker'], $userCoin);
        }
        debug('end');
        $log["lasting"] = debug('begin', 'end', 6) . "s";
        $log["memory"] = debug('begin', 'end', 'm');
        Log::logger('BatchRewards', JSONReturn($log), 'execute');
    }
    return true;
}

/**
 * 批量更新表数据 yt
 * @param $tablename 表名
 * @param $fields 字段数组 字段一定使用到唯一索引
 * @param $data 更新数据 ，二位数组
 * @return int
 * @throws Exception
 */
function BatchUpdate($tablename, $fields, $data)
{
    if (empty($tablename) && empty($fields) && empty($data)) {
        throw new Exception('批量更新缺少必须参数');
    }
    $sql = 'insert into ' . $tablename . ' (' . rtrim(implode(',', $fields), ',') . ') values ';
    foreach ($data as $v) {
        $tmp = '';
        foreach ($v as $vv) {
            $tmp .= '\'' . $vv . '\',';
        }
        $sql .= '(' . rtrim($tmp, ',') . '),';
    }

    $sql = rtrim($sql, ',');

    $sql .= 'on duplicate key update ';

    foreach ($fields as $v) {
        $sql .= $v . '=values(`' . $v . '`),';
    }
    $sql = rtrim($sql, ',') . ';';
    $res = Db::execute($sql);
    return $res;
}

/*获取用户类型 yt*/
function getUserTypeName($userType = '')
{
    $userTypes = cache('users_types');
    if (empty($userTypes)) {
        $typetmp = Db::name('users_type')->field('id,name')->select();
        $userTypes = array_column($typetmp, 'name', 'id');
        cache('users_types', $userTypes, 3600 * 24);
    }
    if (empty($userType)) {
        return $userTypes;
    } else {
        if (empty($userTypes[$userType])) {
            return false;
        } else {
            return $userTypes[$userType];
        }
    }
}