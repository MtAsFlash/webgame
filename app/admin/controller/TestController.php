<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2024-04-17
 * Time: 11:16:25
 * Info: 测试控制器，调用admin插件模块授权访问
 */

namespace app\admin\controller;

use plugin\admin\app\controller\CrudController;

class TestController extends CrudController
{

    /**
     * 不需要登录的方法
     * @var string[]
     */
    protected $noNeedLogin = ['test'];

    /**
     * 不需要权限的方法
     *
     * @var string[]
     */
    protected $noNeedAuth = [];

    public function index()
    {
        return "这里没有权限访问，必须登录后才能看到";
    }

    public function test()
    {
        return "这里可以看到哦，因为 noNeedLogin 赋值了test方法名";
    }

}
