<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/8/5 22:45
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Backup\Tasks\Backup\BackupJobFactory;
use Spatie\Backup\BackupDestination\BackupDestinationFactory;
use Illuminate\Support\Facades\Artisan;


class BackupController extends BaseController
{



    public function backupDb(){
//        try {
//            // 直接调用 Artisan 命令来执行备份
//            Artisan::call('backup:run');
//
//            return response()->json(['message' => 'Backup created successfully.']);
//        } catch (\Exception $e) {
//            return response()->json(['message' => 'Failed to create backup.', 'error' => $e->getMessage()], 500);
//        }
    }

}
