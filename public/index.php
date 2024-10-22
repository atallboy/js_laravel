<?php
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

// 获取当前请求的协议（http或https）
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
// 获取当前请求的主机名（域名）
$original_host = $_SERVER['HTTP_HOST'];
//   echo($original_host);die;
if($original_host=='hbdj.jiuzhouzhichuang.net'){
    if(!array_key_exists('act',$_GET)){die;}

    // 获取当前请求的URI（包括查询字符串和片段）
    $request_uri = $_SERVER['REQUEST_URI'];

    // 新的域名
    $new_host = 'hbdj.guangdongyizhankeji.cn';

    // 拼接成新的URL（只替换主机名部分）
    $new_url = str_replace($original_host, $new_host, $protocol . $original_host . $request_uri);
    //   echo($new_url);die;
    // 重定向到新URL
    header("Location: $new_url");
    exit();
}


if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
