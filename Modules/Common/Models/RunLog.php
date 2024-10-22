<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/9/26 13:50
 * @Description: 版权所有
 */

namespace Modules\Common\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class RunLog
{
    public function recordRunning($request)
    {
        $startTime = microtime(true);


        // 计算运行时间
        $endTime = microtime(true);
        $duration = $endTime - $startTime;
        $memoryUsage = memory_get_usage(true);

        // 日志内容
        $logData = [
            'ip' => $request->ip(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'duration' => $duration,
            'memory_usage' => $memoryUsage,
            'headers' => $request->headers->all(),
            'params' => $request->all(),
        ];

        // 将日志数据转换为 JSON 格式并追加换行
        $logEntry = json_encode($logData) . PHP_EOL;

        // 将日志写入到 1.txt 文件
        Storage::disk('local')->append('1.txt', $logEntry);
    }
}
