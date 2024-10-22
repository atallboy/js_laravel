<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/17 11:31
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;

class UploadController
{
    public function upload(Request $request){
//        $file = $request->file('file');
//        $path = Storage::putFile('/car', $file);
//        $name = $file->getClientOriginalName();
//        $extension = $file->getClientOriginalExtension();
        $path = 'https://'.$_SERVER['HTTP_HOST'].'/upload/'.$request->file('file')->store('/jishi');
        return $path;
    }
}
