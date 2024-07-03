<?php

/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2024-03-01
 * Time: 10:33:53
 * Info:
 */

namespace app\api\controller;

use Http\Client\Exception;
use support\Response;
use support\Request;
use support\exception\BusinessException;

class Base
{

    /**
     * 登录token
     * @var string
     */
    protected $token = '';

    /**
     * 登录会员id
     * @var int
     */
    protected $userId = 0;

    protected $userInfo = [];

    protected $request;

    //应用实例
    protected $app;

    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [];

    public function __construct()
    {
        $this->request  = request();
        $this->userId   = request()->userId ?? "";
        $this->userInfo = request()->userInfo ?? "";
        $this->token    = request()->token ?? "";

    }

    /**
     * 返回格式化json数据
     *
     * @param int    $code
     * @param string $msg
     * @param array  $data
     *
     * @return Response
     */
    protected function json(int $code, string $msg = 'ok', array $data = []): Response
    {
        return json(['code' => $code, 'data' => $data, 'msg' => $msg]);
    }

    protected function success(string $msg = '成功', array $data = [], int $code = 200): Response
    {
        return $this->json($code, $msg, $data);
    }

    protected function error(string $msg = '失败', array $data = [], int $code = 0): Response
    {
        return $this->json($code, $msg, $data);
    }
}
