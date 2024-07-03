<?php

/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2024-03-01
 * Time: 11:52:35
 * Info:
 */

namespace app\api\model;

use Shopwwi\LaravelCache\Cache;
use support\exception\BusinessException;
use support\lib\Random;

class User extends Base
{

    //Token默认有效时长
    protected $keeptime = 3600 * 24;

    //token的有效时间-token创建时间大于3600,秒，执行更新
    protected $lefttime = 3600;

    public function toLogin($username, $password)
    {
        $userInfo = $this->where(['username' => $username])->find();
        if (empty($userInfo)) {
            throw new BusinessException("没有找到此账号");
        }
        if ($userInfo['status'] !== 1) {
            throw new BusinessException("该账号已被禁用");
        }
        //验证密码
        if (cmf_password($password, $userInfo['salt']) !== $userInfo['password']) {
            throw new BusinessException("密码错误");
        }
        if (is_object($userInfo)) {
            $userInfo = $userInfo->toArray();
        }

        //登录成功，存储用户信息
        return $this->setToken($userInfo);
    }

    /**
     * 根据token来获取用户id
     *
     * @param $token
     * @param $status
     *
     * @return void
     */
    public function checkToken($token, $status = 1)
    {
        $data = Cache::get($token);
        if (empty($data)) {
            return false;
        }
        //token快过期的时候，更新token
        $this->keepToken($data);

        return $data;
    }

    /**
     * 登陆存token
     *
     * @param     $userInfo
     * @param int $platform //1就是普通登陆
     *
     * @return array
     */
    public function setToken($userInfo, $platform = 1)
    {
        $createTime             = time();
        $token                  = $this->algorithm($userInfo['id'], $userInfo['password'], $platform, $createTime);
        $userInfo['token']      = $token;
        $userInfo['token_time'] = $createTime;//token创建时间
        Cache::put($token, $userInfo, $this->keeptime);

        return $userInfo;
    }

    /**
     * 自动更新token过期时间
     *
     * @param $userInfo
     *
     * @return void
     */
    public function keepToken($userInfo)
    {
        $token = $userInfo['token'];
        //获取token的创建时间
        $tokenTime = $userInfo['token_time'];
        if (time() - $tokenTime < $this->lefttime) {
            return true;
        }
        //如果token过期时间小于3600秒，执行更新
        $userInfo['token_time'] = time();

        Cache::put($token, $userInfo, $this->keeptime);

        return true;
    }

    private function algorithm($user_id, $password, $platform, $createtime)
    {
        return md5(md5($user_id.$password.$platform.$createtime).rand(1, 10000));
    }
}
