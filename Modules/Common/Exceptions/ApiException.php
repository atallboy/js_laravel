<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/6/16 20:37
 * @Description: 版权所有
 */

namespace Modules\Common\Exceptions;
use Throwable;
class ApiException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
