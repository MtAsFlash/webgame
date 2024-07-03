<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2023-10-09
 * Time: 15:25:54
 * Info:
 */

namespace app\api\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;
use ReflectionClass;
use app\api\model\User;
use support\exception\BusinessException;

class AuthCheckAccess implements MiddlewareInterface
{

    protected $_user = null;

    public function process(Request $request, callable $handler): Response
    {
        //判断是否需要登录
        // 通过反射获取控制器哪些方法不需要登录
        $controller  = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // 访问的方法需要登录
        if ( ! in_array($request->action, $noNeedLogin)) {
            $token = $request->header('token');
            if (empty($token)) {
                // 拦截请求
                return json(['code' => 401, 'msg' => '请先登录']);
            }
            //判断token是否有效
            $userModel = new User();
            $checkData = $userModel->checkToken($token);
            if ( ! $checkData || empty($checkData['id'])) {
                return json(['code' => 401, 'msg' => '登录已失效，请重新登录']);
            }

            $request->userId   = $checkData['id'];
            $request->userInfo = $checkData;
            $request->token    = $checkData['token'];
        }

        // 不需要登录
        return $handler($request);
    }

}
