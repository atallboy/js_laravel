<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/18 12:36
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function rules()
    {
        return [
            'address_id' => 'required|int',
            'master_id' => 'required|int',
            'service_time' => 'required',
        ];
    }

    public function messages()
    {
        return
            [
                'address_id.required' => '地址不能为空',
                'master_id.required' => '服务技师不能为空',
                'service_time.required' => '服务时间不能为空',
            ];
    }
}
