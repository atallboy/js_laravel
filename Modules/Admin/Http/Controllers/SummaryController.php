<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/19 17:06
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Modules\Admin\Services\summary\SummaryService;

class SummaryController extends BaseController
{
    public function index(){
        return (new SummaryService())->index();
    }
}
