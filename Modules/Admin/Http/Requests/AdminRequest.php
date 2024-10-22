<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|int|max:5',
            'account' => 'required'
        ];
    }

    public function messages()
    {
        return
            [
            'name.required' => '名称必传',
            'name.int' => '为整数',
            'name.max' => '最大不能超过5',
            'account.required' => '该项必传',
        ];
    }

}
