<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-04-25
 * Time: 16:58:22
 * Info:
 */

namespace app\api\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AccessControl implements MiddlewareInterface
{

    public function process(Request $request, callable $handler): Response
    {
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        $response->withHeaders([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin'      => $request->header('Origin', '*'),
            'Access-Control-Allow-Methods'     => '*',
            'Access-Control-Allow-Headers'     => '*',
        ]);

        return $response;
    }
}
