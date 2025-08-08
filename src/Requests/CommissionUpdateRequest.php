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
            'category_id' => 'required_if:name,category|exists:categories,id',            
            'commission_type' => 'required|in:percentage,fixed',
            'commission_value' => 'required|numeric|min:0',
            'status' => 'required|boolean',
        ];
    }
}
