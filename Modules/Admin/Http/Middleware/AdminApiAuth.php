<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/6/16 16:03
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Middleware;

use Closure;
class AdminApiAuth
{
    public function handle($request, Closure $next){
        return $next($request);
    }

}
