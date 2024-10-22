<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/6/19 06:56
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CarNumber extends FormRequest
{
    public function rules()
    {
        return [
            'province' => 'required',
            'number_a' => 'required',
            'number_b' => 'required',
            'number_c' => 'required',
            'number_d' => 'required',
            'number_e' => 'required',
            'number_f' => 'required',
            'number_type' => 'required',
        ];
    }

    public function messages()
    {
        return
            [
                'province.required' => '车牌号码未填写完整',
                'number_a.required' => '车牌号码未填写完整',
                'number_b.required' => '车牌号码未填写完整',
                'number_c.required' => '车牌号码未填写完整',
                'number_d.required' => '车牌号码未填写完整',
                'number_e.required' => '车牌号码未填写完整',
                'number_f.required' => '车牌号码未填写完整',
                'number_type.required' => '缺少车牌类型',
            ];
    }
}
