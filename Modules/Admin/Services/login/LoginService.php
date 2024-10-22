<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/10/13 18:05
 * @Description: 版权所有
 */

namespace Modules\Admin\Services\login;

class LoginService
{
    public function login($admin){
        session([env('ADMIN_TOKEN_NAME') => $admin->token]);
    }
}
