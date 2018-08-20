<?php

namespace app\admin\behavior;
/**
 * 检测有没有是否登录，访问权限，写入访问日志
 */
class ListenAction
{
    public function run(&$params)
    {
        $allowUrl = [
            'admin/login/index',
            'admin/login/checklogin',
            'admin/login/logout',
            'admin/login/getverify',
        ];
        $staff = session('sk_staff');
        $privileges = session('sk_staff.privileges');
        $urls = cache('AllListenUrl');
        $request = request();
        $visit = strtolower($request->module() . "/" . $request->controller() . "/" . $request->action());
        if (empty($staff) && !in_array($visit, $allowUrl)) {
            if ($request->isAjax()) {
                echo json_encode(['status' => -999, 'msg' => '对不起，您还没有登录，请先登录']);
            } else {
                header("Location:" . url('admin/login/index'));
            }
            exit();
        } else {
            if (array_key_exists($visit, $urls) && !in_array($urls[$visit]['code'], $privileges)) {
                if ($request->isAjax()) {
                    echo json_encode(['status' => -998, 'msg' => '对不起，您没有操作权限，请与管理员联系']);
                } else {
                    header("Content-type: text/html; charset=utf-8");
                    echo "对不起，您没有操作权限，请与管理员联系";
                }
                exit();
            }
            if (array_key_exists($visit, $urls)) {
                $data = [];
                $data['menuId'] = $urls[$visit]['menuId'];
                $data['operateUrl'] = $_SERVER['REQUEST_URI'];
                $data['operateDesc'] = $urls[$visit]['name'];
                $data['content'] = !empty($_REQUEST) ? json_encode($_REQUEST) : '';
                $data['operateIP'] = $request->ip();
                model('admin/LogOperates')->add($data);
            }
        }
    }
}