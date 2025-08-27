<?php

namespace admin\commissions\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommissionUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'type' => 'required|max:255',
            'category_ids' => 'required_if:type,category|array',
            'category_ids.*' => 'exists:categories,id',
            'commission_type' => 'required|in:percentage,fixed',
            'commission_value' => 'required|numeric|min:0',
            'status' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'type.required' => 'Please select a commission type.',
            'type.in' => 'The selected commission type is invalid.',
            'category_ids.required_if' => 'Please select at least one category when type is category.',
            'category_ids.array' => 'Categories must be an array.',
            'category_ids.*.distinct' => 'Duplicate categories are not allowed.',
            'category_ids.*.exists' => 'Selected category is invalid.',
            'commission_type.required' => 'Please select a commission type.',
            'commission_type.in' => 'The selected commission type is invalid.',
            'commission_value.required' => 'Please enter a commission value.',
            'commission_value.numeric' => 'Commission value must be a number.',
            'commission_value.min' => 'Commission value must be at least 0.',
            'status.required' => 'Please select a status.',
            'status.boolean' => 'The status must be true or false.',
        ];
    }
}
