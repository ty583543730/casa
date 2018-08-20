<?php

/**
 * 判断有没有权限
 * @param $code 权限代码
 * @param $type 返回的类型  true-boolean   false-string
 */
function SKgrant($code)
{
    if (in_array($code, session("sk_staff.privileges"))) return true;
    return false;
}