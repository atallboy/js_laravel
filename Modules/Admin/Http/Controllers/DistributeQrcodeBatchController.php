<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/10/9 13:47
 * @Description: 版权所有
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\distribute\DistributeQrcodeBatchService as service;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DistributeQrcodeBatchController extends BaseController
{
    function index(Request $request)
    {
        return (new service())->index($request->only(['page','limit','status','name']));
    }

    public function del(Request $request){
        return (new service())->del($request->input('id'));
    }

    function download(Request $request)
    {

        $batch_id = $request->input('batch_id');
        if($batch_id){
            $list = DB::table('distribute_qrcode')->where('create_record_id',$batch_id)->where('del',1)->get();
            $filesToCopy = [];
            $i=0;
            foreach ($list as $k=>$v){
                $filesToCopy[] = ($v->pic);
            }

            $_targetDir = ('upload/temp/'.time());
            $targetDir = public_path($_targetDir);
            $zipFileName = $targetDir."/$batch_id.zip"; // 指定压缩文件的路径

            if (!is_dir($targetDir)) {
                if (!mkdir($targetDir, 0777, true)) {
                    die("无法创建目录 $targetDir");
                }
            }

            foreach ($filesToCopy as $file) {
                $sourceFile = public_path($file);
                $filename =basename($file);
                $targetFile = "$targetDir/$filename";
                if (copy($sourceFile, $targetFile)) {
                    $i++;
                }
            }

            // 压缩目录
            $zip = new ZipArchive();

            if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($targetDir),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );
                foreach ($files as $name => $file) {
                    // 跳过目录（它们会以/结尾）
                    if (!$file->isDir()) {
                        // 获取文件的相对路径
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($targetDir) + 1);

                        // 将文件添加到压缩包中
                        $zip->addFile($filePath, $relativePath);
                    }
                }
                $zip->close();
                return $this->apiSuccess($i,$this->host.'/'.$_targetDir."/$batch_id.zip");
            } else {
                // 处理压缩失败的情况
                return response()->json(['error' => 'Failed to open zip file for writing.'], 500);
            }

        }

    }
}

