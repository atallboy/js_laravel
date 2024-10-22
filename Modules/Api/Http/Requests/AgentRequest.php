<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/6/17 16:22
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'tel' => 'required'
        ];
    }

    public function messages()
    {
        return
            [
                'name.required' => '名称必传',
                'tel.required' => '电话必传',
            ];
    }

}
