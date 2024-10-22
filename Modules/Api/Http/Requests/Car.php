<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/6/19 09:56
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Car extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'car_number_id' => 'required|int',
            'gender' => 'required',
            'brand' => 'required',
            'model' => 'required',
            'tel' => 'required',
            'answer_way' => 'required',
            'wechat' => 'required',
            'message' => 'required',
        ];
    }

    public function messages()
    {
        return
            [
                'name.required' => '表单未填写完整',
            ];
    }
}
