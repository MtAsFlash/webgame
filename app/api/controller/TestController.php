<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2024-03-01
 * Time: 10:39:35
 * Info: 测试接口
 */

namespace app\api\controller;

use app\api\model\User;
use Shopwwi\LaravelCache\Cache;
use support\Container;

class TestController extends Base
{

    /**
     * 无需登录及鉴权的方法
     * @var array
     */
    protected $noNeedLogin = ['index', 'login'];

    /**
     * 无需登录即可获取数据
     * @return void
     */
    public function index()
    {
        $config = ['config' => 1];

        return $this->success("获取成功", $config);
    }

    /*
     * 登录接口
     */
    public function login()
    {
        $username = request()->post('username');
        $password = request()->post('password');
        //登录
        try {
            $result = (new User())->toLogin($username, $password);
            if ( ! empty($result)) {
                return $this->success('登录成功', $result);
            } else {
                return $this->error('登录失败');
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

    }

    /**
     * 需要登录成功之后才能获取数据
     * @return void
     */
    public function needLogin()
    {
        $userId   = $this->userId;
        $userInfo = $this->userInfo;

        return $this->success('ok', ['user_id' => $userId, 'user_info' => $userInfo]);
    }

    /**
     * 退出登录
     * @return void
     */
    public function logout()
    {
        //删除token对应的值即可
        $token = $this->token;
        Cache::forget($token);

        return $this->success('退出成功');
    }

}
